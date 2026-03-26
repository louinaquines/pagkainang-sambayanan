<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

/**
 * NOTE: This controller is unused/dead code.
 * Emergency mode toggling is handled by AdminController::toggleEmergencyMode().
 * This can be safely deleted.
 */
class EmergencyModeController extends Controller
{
    /**
     * Update the emergency mode setting.
     */
    public function update(Request $request)
    {
        if (! $request->user() || $request->user()->role !== 'admin') {
            abort(403);
        }

        $validated = $request->validate([
            'emergency_mode' => ['nullable', 'boolean'],
        ]);

        $settings = Setting::firstOrCreate([], [
            'emergency_mode' => false,
        ]);

        $settings->emergency_mode = $request->boolean('emergency_mode');
        $settings->save();

        return back()->with('status', 'emergency-mode-updated');
    }
}

