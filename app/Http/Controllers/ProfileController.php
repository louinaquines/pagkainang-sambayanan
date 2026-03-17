<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Update charity's emergency/area status.
     */
    public function updateEmergencyStatus(Request $request): RedirectResponse
    {
        $request->validate([
            'area_severity' => 'required|integer|min:1|max:4',
            'population_count' => 'required|integer|min:0',
            'accessibility' => 'required|integer|min:0|max:100',
        ]);

        $user = $request->user();
        
        if ($user->role !== 'charity') {
            return Redirect::back()->with('error', 'Only charities can update emergency status.');
        }

        $user->update([
            'area_severity' => $request->area_severity,
            'population_count' => $request->population_count,
            'accessibility' => $request->accessibility,
        ]);

        return Redirect::back()->with('success', 'Emergency status updated successfully!');
    }
}
