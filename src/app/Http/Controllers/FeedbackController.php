<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Models\Donation;
use App\Models\Notification;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $pendingDonations = collect(); // donations awaiting feedback (charity only)

        if ($user->role === 'admin') {
            $feedbacks = Feedback::with(['donation.donor', 'donation.charity', 'charity'])
                ->latest()
                ->get();
        } elseif ($user->role === 'donor') {
            // Donor sees feedback on their donations
            $feedbacks = Feedback::with(['donation', 'charity'])
                ->whereHas('donation', fn($q) => $q->where('user_id', $user->id))
                ->latest()
                ->get();
        } else {
            // Charity sees their submitted feedbacks
            $feedbacks = Feedback::with(['donation.donor', 'charity'])
                ->where('charity_id', $user->id)
                ->latest()
                ->get();

            // Also load donations they received that still have no feedback
            $submittedDonationIds = $feedbacks->pluck('donation_id');
            $pendingDonations = Donation::with('donor')
                ->where('claimed_by', $user->id)
                ->whereNotNull('claimed_by')
                ->when($submittedDonationIds->isNotEmpty(), fn($q) =>
                    $q->whereNotIn('id', $submittedDonationIds)
                )
                ->latest()
                ->get();
        }

        return view('feedback.index', compact('feedbacks', 'pendingDonations'));
    }

    public function create($donationId)
    {
        $donation = Donation::with('donor')->findOrFail($donationId);

        // Only the charity that received this donation may submit feedback
        if ($donation->claimed_by !== auth()->id()) {
            abort(403);
        }

        return view('feedback.create', compact('donation'));
    }

    public function store(Request $request, $donationId)
    {
        $donation = Donation::with('donor')->findOrFail($donationId);

        // Only the charity that received this donation may submit feedback
        if ($donation->claimed_by !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'message' => 'required|string|max:1000',
            'photo'   => 'nullable|image|max:4096',
            'food_quality_rating' => 'nullable|integer|min:1|max:5',
            'quantity_rating' => 'nullable|integer|min:1|max:5',
        ]);

        $path = null;
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('feedback-photos', 'public');
        }

        Feedback::create([
            'donation_id' => $donationId,
            'charity_id'  => auth()->id(),
            'message'     => $request->message,
            'photo_path'  => $path,
            'food_quality_rating' => $request->food_quality_rating,
            'quantity_rating' => $request->quantity_rating,
        ]);

        $donation->update(['status' => 'completed']);

        // Notify the donor that their donation reached those in need
        Notification::create([
            'user_id'    => $donation->user_id,
            'type'       => 'feedback_received',
            'message'    => auth()->user()->organization_name . ' has confirmed receipt of your donation "' . $donation->description . '" and submitted a distribution report.',
            'related_id' => $donation->id,
            'is_read'    => false,
        ]);

        return redirect()->route('feedback.index')
            ->with('success', 'Feedback submitted successfully.');
    }

    public function comment(Request $request, $feedbackId)
    {
        $request->validate([
            'charity_comment' => 'required|string|max:1000',
        ]);

        $feedback = Feedback::findOrFail($feedbackId);

        if ($feedback->charity_id !== auth()->id()) {
            abort(403);
        }

        $feedback->update([
            'charity_comment' => $request->charity_comment,
        ]);

        return redirect()->back()->with('success', 'Comment saved.');
    }
}