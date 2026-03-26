<?php

namespace App\Http\Controllers;

use App\Models\CharityRequest;
use App\Models\Donation;
use App\Models\Notification;
use Illuminate\Http\Request;

class CharityRequestController extends Controller
{
    public function create()
    {
        return view('charity.request-create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'food_name'   => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'quantity'    => 'nullable|string|max:100',
            'urgency'     => 'required|in:normal,urgent,critical',
        ]);

        CharityRequest::create([
            'charity_id'  => auth()->id(),
            'food_name'   => $request->food_name,
            'description' => $request->description,
            'quantity'    => $request->quantity,
            'urgency'     => $request->urgency,
            'status'      => 'open',
        ]);

        return redirect()->route('dashboard')
            ->with('success', 'Food request posted successfully!');
    }

    public function index()
    {
        $user = auth()->user();
        
        // If user is a charity, show their own requests
        if ($user->role === 'charity') {
            $myRequests = CharityRequest::where('charity_id', $user->id)
                ->latest()
                ->get();
            
            // Also get all open requests from other charities for reference
            $otherRequests = CharityRequest::with('charity')
                ->where('status', 'open')
                ->where('charity_id', '!=', $user->id)
                ->latest()
                ->get();
                
            return view('charity.requests-index', compact('myRequests', 'otherRequests'));
        }
        
        // For donors and admins, show all open requests
        $requests = CharityRequest::with('charity')
            ->where('status', 'open')
            ->latest()
            ->get();

        return view('charity.requests-index', compact('requests'));
    }

    // Donor fulfills a charity request directly
    public function fulfill(Request $request, $id)
    {
        $charityRequest = CharityRequest::with('charity')->findOrFail($id);

        // Create the donation and immediately assign it as claimed
        $donation = Donation::create([
            'description'     => 'Donation for: ' . $charityRequest->food_name,
            'target_audience' => 'Charity Organization',
            'user_id'         => auth()->id(),
            'charity_id'      => $charityRequest->charity_id,
            'claimed_by'      => $charityRequest->charity_id,
            'status'          => 'completed',
        ]);

        // Mark the charity request as fulfilled
        $charityRequest->update(['status' => 'fulfilled']);

        // Notify the charity
        Notification::create([
            'user_id'    => $charityRequest->charity_id,
            'type'       => 'donation_received',
            'message'    => auth()->user()->name . ' donated in response to your request for "' . $charityRequest->food_name . '".',
            'related_id' => $donation->id,
            'is_read'    => false,
        ]);

        return redirect()->route('charity.requests.index')
            ->with('success', 'Your donation has been sent to ' . $charityRequest->charity->name . '!');
    }
}