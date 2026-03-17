<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class CharityController extends Controller
{
    public function showRegistrationForm()
    {
        return view('charity.register');
    }

    public function submitRegistration(Request $request)
    {
        $request->validate([
            'organization_name'        => 'required|string|max:255',
            'organization_description' => 'required|string',
            'contact_number'           => 'required|string|max:20',
            'address'                  => 'required|string',
            'legitimacy_document'      => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $path = $request->file('legitimacy_document')
            ->store('legitimacy_documents', 'public');

        auth()->user()->update([
            'role'                     => 'charity',
            'organization_name'        => $request->organization_name,
            'organization_description' => $request->organization_description,
            'contact_number'           => $request->contact_number,
            'address'                  => $request->address,
            'legitimacy_document'      => $path,
            'verification_status'      => 'pending',
        ]);

        return redirect()->route('dashboard')
            ->with('success', 'Your charity registration has been submitted for review.');
    }
}