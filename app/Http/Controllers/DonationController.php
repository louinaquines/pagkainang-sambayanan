<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Models\DonationClaim;
use App\Models\Feedback;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DonationController extends Controller
{
    public function create()
    {
        $charities = User::where('role', 'charity')
            ->where('verification_status', 'approved')
            ->orderBy('name')
            ->get();

        $emergencyMode = DB::table('settings')->where('key', 'emergency_mode')->value('value') === '1';

        return view('donations.create', compact('charities', 'emergencyMode'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'description'     => 'required|string|max:1000',
            'target_audience' => 'required|string|max:255',
            'charity_id'      => 'nullable|exists:users,id',
        ]);

        $donationData = [
            'description'     => $request->description,
            'target_audience' => $request->target_audience,
            'charity_id'      => $request->charity_id ?: null,
            'user_id'         => auth()->id(),
            'status'          => 'available',
            'affected_area'   => $request->affected_area ?: null,
        ];

        if ($request->has('area_severity')) {
            $donationData['area_severity'] = $request->area_severity;
        }
        if ($request->has('expires_at') && $request->expires_at) {
            $donationData['expires_at'] = $request->expires_at;
        }

        $donation = Donation::create($donationData);

        $emergencyMode = DB::table('settings')->where('key', 'emergency_mode')->value('value') === '1';
        $suggestionMessage = '';
        if ($emergencyMode) {
            $topCharity = User::where('role', 'charity')
                ->where('verification_status', 'approved')
                ->get()
                ->map(function($charity) {
                    $severity = ($charity->area_severity ?? 1) * 1.25;
                    $severity = min(5, max(1, $severity));
                    $population = min(1, ($charity->population_count ?? 0) / 100000);
                    $accessibility = ($charity->accessibility ?? 100) / 100;
                    $priority = ($severity * 0.5) + ($population * 0.3) + ((1 - $accessibility) * 0.2);
                    $charity->priority_score = round($priority, 2);
                    return $charity;
                })
                ->sortByDesc('priority_score')
                ->first();

            if ($topCharity) {
                $suggestionMessage = "Your donation has been posted! Based on the Priority Index, {$topCharity->organization_name} (Score: {$topCharity->priority_score}) is the highest priority. The admin will review and allocate this donation.";
            } else {
                $suggestionMessage = 'Your donation has been posted! No verified charities available for allocation yet.';
            }
        }

        return redirect()->route('dashboard')
            ->with('donation_success', $suggestionMessage ?: 'Your donation has been posted!');
    }

    public function available()
    {
        $user = auth()->user();
        $emergencyMode = DB::table('settings')->where('key', 'emergency_mode')->value('value') === '1';

        if ($user->role === 'charity') {
            $donations = Donation::with(['donor', 'charity', 'feedback', 'claims'])
                ->where('status', 'available')
                ->where(function ($q) use ($user) {
                    $q->whereNull('charity_id')
                      ->orWhere('charity_id', $user->id);
                })
                ->latest()
                ->get();
        } else {
            $donations = Donation::with(['donor', 'charity', 'claims.charity'])
                ->where('status', 'available')
                ->latest()
                ->get();
        }

        return view('donations.available', compact('donations', 'emergencyMode'));
    }

    public function index()
    {
        $user = auth()->user();

        if ($user->role === 'admin') {
            $donations = Donation::with(['donor', 'charity', 'claimedBy', 'feedback.charity'])
                ->latest()->paginate(5);
        } elseif ($user->role === 'donor') {
            $donations = Donation::with(['charity', 'claimedBy', 'claims.charity', 'feedback.charity'])
                ->where('user_id', $user->id)
                ->latest()->paginate(5);
        } else {
            $donations = Donation::with(['donor', 'feedback'])
                ->where('claimed_by', $user->id)
                ->latest()->paginate(5);
        }

        return view('donations.index', compact('donations'));
    }

    public function claim($id)
    {
        $donation = Donation::findOrFail($id);
        $user     = auth()->user();

        $emergencyMode = DB::table('settings')->where('key', 'emergency_mode')->value('value') === '1';
        if ($emergencyMode) {
            return redirect()->back()->with('error', 'Direct claims are blocked during Emergency Mode. Please wait for admin allocation.');
        }

        if ($donation->status !== 'available') {
            return redirect()->back()->with('error', 'This donation is no longer available.');
        }

        $existing = DonationClaim::where('donation_id', $id)
            ->where('charity_id', $user->id)
            ->first();

        if ($existing) {
            return redirect()->back()->with('error', 'You have already requested this donation.');
        }

        DonationClaim::create([
            'donation_id' => $id,
            'charity_id'  => $user->id,
            'status'      => 'pending',
        ]);

        \App\Models\Notification::create([
            'user_id'    => $donation->user_id,
            'type'       => 'claim_request',
            'message'    => $user->organization_name . ' has requested your donation: "' . $donation->description . '"',
            'related_id' => $donation->id,
            'is_read'    => false,
        ]);

        return redirect()->back()->with('success', 'Request sent! The donor will review and accept.');
    }

    public function acceptClaim($claimId)
    {
        $claim    = DonationClaim::with('donation')->findOrFail($claimId);
        $donation = $claim->donation;
        $user     = auth()->user();

        if ($donation->user_id !== $user->id) {
            abort(403);
        }

        if ($donation->status !== 'available') {
            return redirect()->back()->with('error', 'This donation has already been claimed.');
        }

        DonationClaim::where('donation_id', $donation->id)
            ->where('id', '!=', $claimId)
            ->update(['status' => 'rejected']);

        $claim->update(['status' => 'accepted']);

        $donation->update([
            'claimed_by' => $claim->charity_id,
            'claimed_at' => now(),
            'status'     => 'completed',
        ]);

        \App\Models\Notification::create([
            'user_id'    => $claim->charity_id,
            'type'       => 'claim_accepted',
            'message'    => 'Your request for "' . $donation->description . '" has been accepted by the donor!',
            'related_id' => $donation->id,
            'is_read'    => false,
        ]);

        // Also notify the donor that their donation has been accepted/completed
        \App\Models\Notification::create([
            'user_id'    => $donation->user_id,
            'type'       => 'donation_accepted',
            'message'    => 'Your donation "' . $donation->description . '" has been accepted and assigned to ' . $claim->charity->organization_name . '!',
            'related_id' => $donation->id,
            'is_read'    => false,
        ]);

        return redirect()->back()->with('success', 'Donation accepted and assigned to charity.');
    }

    public function destroyBulk(Request $request)
    {
        $request->validate([
            'ids'   => 'required|array|min:1',
            'ids.*' => 'integer|exists:donations,id',
        ]);

        $user = auth()->user();
        $ids  = $request->ids;

        $query = Donation::whereIn('id', $ids);

        if ($user->role === 'donor') {
            $query->where('user_id', $user->id);
        } elseif ($user->role === 'charity') {
            $query->where('claimed_by', $user->id);
        }

        $donations = $query->get();
        $count = $donations->count();

        foreach ($donations as $donation) {
            $donation->delete();
        }

        return redirect()->route('donations.index')
            ->with('success', $count . ' donation record(s) hidden from history. They are still counted in totals.');
    }
}