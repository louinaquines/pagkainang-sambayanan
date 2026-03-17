<?php

namespace App\Http\Controllers;

use App\Models\CharityRequest;
use App\Models\Donation;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class AdminController extends Controller
{
    /**
     * Calculate Priority Score using Weighted Scoring Model
     * Formula: Priority = (Severity × 0.5) + (Population × 0.3) + ((1 - Accessibility) × 0.2)
     */
    public function calculatePriorityScore(User $charity): float
    {
        // Severity: 1-5 (we have 1-4, so multiply by 1.25 to get 1-5)
        $severity = ($charity->area_severity ?? 1) * 1.25;
        $severity = min(5, max(1, $severity));
        
        // Population: normalize to 0-1 range (assuming max 100000)
        $population = min(1, ($charity->population_count ?? 0) / 100000);
        
        // Accessibility: already 0-100, convert to 0-1
        $accessibility = ($charity->accessibility ?? 100) / 100;
        $inaccessibility = 1 - $accessibility;
        
        // Calculate priority score
        $priority = ($severity * 0.5) + ($population * 0.3) + ($inaccessibility * 0.2);
        
        return round($priority, 2);
    }

    /**
     * Check if location is CRITICAL (Severity 5 AND Accessibility 0)
     */
    public function isCritical(User $charity): bool
    {
        $severity = ($charity->area_severity ?? 1) * 1.25;
        $accessibility = $charity->accessibility ?? 100;
        
        return ($severity >= 4.5) && ($accessibility <= 10);
    }

    public function dashboard()
    {
        $emergencyMode = DB::table('settings')->where('key', 'emergency_mode')->value('value');
        
        // Fetch ReliefWeb disasters (cached for 30 minutes)
        $disasters = [];
        try {
            $disasters = Cache::remember('reliefweb_disasters', 1800, function () {
                $response = Http::get('https://api.reliefweb.int/v1/disasters', [
                    'filter[field][country]' => 'Philippines',
                    'limit' => 5
                ]);
                
                if ($response->successful()) {
                    $data = $response->json();
                    return $data['data'] ?? [];
                }
                return [];
            });
        } catch (\Exception $e) {
            // If API fails, return empty array - dashboard still loads
            $disasters = [];
        }
        
        $emergencyData = null;
        if ($emergencyMode == '1') {
            // Get all approved charities with their priority scores
            $charities = User::where('role', 'charity')
                ->where('verification_status', 'approved')
                ->get()
                ->map(function($charity) {
                    $charity->priority_score = $this->calculatePriorityScore($charity);
                    $charity->is_critical = $this->isCritical($charity);
                    
                    // Fetch weather data for charity's city (cached for 30 minutes)
                    $charity->weather = $this->getWeatherForCity($charity->address);
                    
                    return $charity;
                })
                ->sortByDesc('priority_score')
                ->values();
            
            $topCharity = $charities->first();
            
            // Get donations sorted by area severity and expiry
            $topDonation = Donation::where('status', 'available')
                ->orderByDesc('area_severity')
                ->orderBy('expires_at', 'asc')
                ->first();
            
            // Get all open charity requests sorted by urgency and priority score
            $charityRequests = CharityRequest::with('charity')
                ->where('status', 'open')
                ->get()
                ->sortByDesc(function($req) {
                    $urgencyMap = ['normal' => 1, 'urgent' => 2, 'critical' => 3];
                    $urgency = $urgencyMap[$req->urgency] ?? 1;
                    $areaSeverity = $req->charity->area_severity ?? 1;
                    $population = $req->charity->population_count ?? 0;
                    return ($urgency * 100) + ($areaSeverity * 10) + ($population / 10000);
                })
                ->values();
            
            $emergencyData = [
                'charities' => $charities,
                'topCharity' => $topCharity,
                'topDonation' => $topDonation,
                'charityRequests' => $charityRequests,
            ];
        }
        
        return view('admin.dashboard', compact('emergencyMode', 'emergencyData', 'disasters'));
    }
    
    /**
     * Get weather data for a city (cached for 30 minutes)
     */
    private function getWeatherForCity(?string $city): array
    {
        if (empty($city)) {
            return [];
        }
        
        // Create a cache key from the city name
        $cacheKey = 'weather_' . strtolower(str_replace(' ', '_', $city));
        
        try {
            return Cache::remember($cacheKey, 1800, function () use ($city) {
                $apiKey = env('OPENWEATHER_API_KEY');
                
                if (empty($apiKey)) {
                    return [];
                }
                
                $response = Http::get("https://api.openweathermap.org/data/2.5/weather", [
                    'q' => $city . ', Philippines',
                    'appid' => $apiKey,
                    'units' => 'metric'
                ]);
                
                if ($response->successful()) {
                    $data = $response->json();
                    return [
                        'temperature' => $data['main']['temp'] ?? null,
                        'condition' => $data['weather'][0]['main'] ?? null,
                        'description' => $data['weather'][0]['description'] ?? null,
                        'icon' => $data['weather'][0]['icon'] ?? null,
                        'city' => $data['name'] ?? $city,
                        'humidity' => $data['main']['humidity'] ?? null,
                    ];
                }
                return [];
            });
        } catch (\Exception $e) {
            return [];
        }
    }

    public function toggleEmergencyMode()
    {
        $current = DB::table('settings')->where('key', 'emergency_mode')->value('value');
        $new = $current == '1' ? '0' : '1';

        DB::table('settings')->updateOrInsert(
            ['key' => 'emergency_mode'],
            ['value' => $new]
        );

        $message = $new === '1' ? 'Emergency Mode activated.' : 'Emergency Mode deactivated.';
        return redirect()->route('dashboard')->with('success', $message);
    }

    /**
     * Confirm allocation - assigns donation to charity based on admin decision
     */
    public function confirmAllocation(Request $request)
    {
        $donationId = $request->donation_id;
        $charityId = $request->charity_id;

        $donation = Donation::findOrFail($donationId);
        $charity = User::findOrFail($charityId);

        // Assign donation to charity
        $donation->update([
            'claimed_by' => $charity->id,
            'claimed_at' => now(),
            'status' => 'completed',
        ]);

        // Notify the charity
        Notification::create([
            'user_id' => $charity->id,
            'type' => 'allocation_confirmed',
            'message' => 'Admin has allocated a donation to your organization: "' . $donation->description . '".',
            'related_id' => $donation->id,
            'is_read' => false,
        ]);

        return redirect()->back()->with('success', 'Allocation confirmed! The charity has been notified.');
    }

    public function charities()
    {
        // Only show charities who have actually submitted an application (have organization_name)
        $charities = \App\Models\User::where('role', 'charity')
            ->whereNotNull('organization_name')
            ->where('organization_name', '!=', '')
            ->get();

        return view('admin.charities', compact('charities'));
    }

    public function approveCharity(Request $request, $id)
    {
        $charity = \App\Models\User::findOrFail($id);
        $charity->update([
            'verification_status' => 'approved',
            'area_severity' => $request->area_severity ?? 1,
            'population_count' => $request->population_count ?? 0,
            'accessibility' => $request->accessibility ?? 100,
        ]);

        Notification::create([
            'user_id'    => $charity->id,
            'type'       => 'charity_approved',
            'message'    => 'Congratulations! Your charity registration has been approved. You can now claim donations and post food requests.',
            'related_id' => $charity->id,
            'is_read'    => false,
        ]);

        return redirect()->back()->with('success', 'Charity approved successfully.');
    }

    public function rejectCharity($id)
    {
        $charity = \App\Models\User::findOrFail($id);
        $charity->update(['verification_status' => 'rejected']);

        Notification::create([
            'user_id'    => $charity->id,
            'type'       => 'charity_rejected',
            'message'    => 'Your charity registration application has been reviewed and was not approved at this time. Please contact us for more information.',
            'related_id' => $charity->id,
            'is_read'    => false,
        ]);

        return redirect()->back()->with('success', 'Charity application rejected.');
    }
}