<x-app-layout>
    <x-slot name="header">Priority Dashboard</x-slot>

    @php
        $totalDonations  = \App\Models\Donation::withTrashed()->count();
        $availableCount  = \App\Models\Donation::withTrashed()->where('status','available')->count();
        $completedCount  = \App\Models\Donation::withTrashed()->whereNotNull('claimed_by')->count();
        $totalCharities  = \App\Models\User::where('role','charity')->count();
        $pendingCount    = \App\Models\User::where('role','charity')->where('verification_status','pending')->count();
        $approvedCount   = \App\Models\User::where('role','charity')->where('verification_status','approved')->count();
        $totalDonors     = \App\Models\User::where('role','donor')->count();
        $totalFeedbacks  = \App\Models\Feedback::count();
        $emergencyMode   = \DB::table('settings')->where('key','emergency_mode')->value('value') ?? '0';
        $isEmergency     = $emergencyMode == '1';
    @endphp





    {{-- ═══ PRIORITY SCORING DASHBOARD ═══ --}}
    @php
        $rankedCharities = \App\Models\User::where('role','charity')
            ->where('verification_status','approved')
            ->get()
            ->map(function($c) {
                $severity     = min(5, max(1, ($c->area_severity ?? 1) * 1.25));
                $population   = min(1, ($c->population_count ?? 0) / 100000);
                $inaccessibility = 1 - (($c->accessibility ?? 100) / 100);
                $c->priority_score = round(($severity * 0.5) + ($population * 0.3) + ($inaccessibility * 0.2), 2);
                $c->is_critical    = ($severity >= 4.5) && (($c->accessibility ?? 100) <= 10);
                
                // Fetch weather for charity's city (cached for 30 minutes)
                $c->weather = \Illuminate\Support\Facades\Cache::remember('weather_' . strtolower(str_replace(' ', '_', $c->address ?? 'unknown')), 1800, function() use ($c) {
                    try {
                        $apiKey = env('OPENWEATHER_API_KEY');
                        if (empty($apiKey) || empty($c->address)) {
                            return [];
                        }
                        $response = \Illuminate\Support\Facades\Http::get("https://api.openweathermap.org/data/2.5/weather", [
                            'q' => $c->address . ', Philippines',
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
                                'city' => $data['name'] ?? $c->address,
                                'humidity' => $data['main']['humidity'] ?? null,
                            ];
                        }
                    } catch (\Exception $e) {
                        // Return empty on error
                    }
                    return [];
                });
                
                return $c;
            })
            ->sortByDesc('priority_score')
            ->values();

        $maxScore = $rankedCharities->max('priority_score') ?: 1;

        $severityLabels = [1 => 'Low', 2 => 'Moderate', 3 => 'High', 4 => 'Critical'];
        $severityColors = [1 => '#1A7A40', 2 => 'var(--gold)', 3 => '#F97316', 4 => 'var(--red)'];
    @endphp

    <div style="margin-bottom:14px;">
        <div class="section-label">Disaster Response</div>
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
            <h2 style="font-family:'Cormorant Garamond',serif;font-size:32px;font-weight:700;color:var(--dark);margin:0;">Priority Scoring Dashboard</h2>
            <div style="display:flex;align-items:center;gap:8px;font-size:12px;color:var(--muted);">
                <span style="font-weight:700;color:var(--dark);">Formula:</span>
                (Severity × 0.5) + (Population × 0.3) + (Inaccessibility × 0.2)
            </div>
        </div>
    </div>

    {{-- Disaster Alert Panel (shown when Emergency Mode is active) --}}
    @if($isEmergency && !empty($disasters))
        <div style="margin-bottom:32px;">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px;">
                <div style="width:10px;height:10px;background:var(--red);border-radius:50%;animation:pulse 2s infinite;"></div>
                <span style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:1.5px;color:var(--red);">Active Disasters in Philippines</span>
            </div>
            <div style="display:grid;grid-template-columns:repeat(5,1fr);gap:16px;">
                @foreach($disasters as $disaster)
                    @php
                        $fields = $disaster['fields'] ?? [];
                        $title = $fields['name'] ?? 'Unknown Disaster';
                        $date = isset($fields['date']['created']) ? date('M d, Y', strtotime($fields['date']['created'])) : 'N/A';
                        $status = $fields['status'] ?? 'unknown';
                        $country = $fields['country']['name'] ?? '';
                        
                        $statusColors = [
                            'current' => 'var(--red)',
                            'past' => 'var(--muted)',
                            'alert' => '#F97316',
                        ];
                        $statusColor = $statusColors[$status] ?? 'var(--muted)';
                    @endphp
                    <div style="background:linear-gradient(135deg,var(--red),#8B2500);border-radius:12px;padding:16px;color:white;">
                        <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:1px;opacity:0.8;margin-bottom:6px;">{{ $date }}</div>
                        <div style="font-size:13px;font-weight:700;line-height:1.4;margin-bottom:8px;">{{ Str::limit($title, 60) }}</div>
                        <div style="display:flex;align-items:center;gap:6px;">
                            <span style="font-size:10px;padding:3px 8px;background:rgba(255,255,255,0.2);border-radius:99px;text-transform:uppercase;">{{ $status }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="card" style="overflow:hidden;margin-bottom:48px;">

        {{-- Legend bar --}}
        <div style="display:flex;align-items:center;gap:24px;padding:16px 28px;border-bottom:1px solid rgba(196,144,16,0.1);background:var(--lighter);flex-wrap:wrap;">
            <div style="font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:1px;">Severity Scale</div>
            @foreach($severityLabels as $level => $label)
                @php $color = $severityColors[$level]; @endphp
                <div style="display:flex;align-items:center;gap:6px;">
                    <div style="width:10px;height:10px;border-radius:3px;background:{{ $color }};"></div>
                    <span style="font-size:12px;color:var(--dark);font-weight:600;">{{ $level }} — {{ $label }}</span>
                </div>
            @endforeach
            <div style="margin-left:auto;font-size:12px;color:var(--muted);">{{ $rankedCharities->count() }} verified {{ Str::plural('charity', $rankedCharities->count()) }}</div>
        </div>

        @if($rankedCharities->isEmpty())
            <div style="padding:60px 40px;text-align:center;">
                <div style="font-family:'Cormorant Garamond',serif;font-size:22px;font-weight:700;color:var(--dark);margin-bottom:8px;">No verified charities yet</div>
                <p style="font-size:13px;color:var(--muted);">Approve charity applications to see their priority scores here.</p>
            </div>
        @else
            <div style="padding:24px 28px;display:flex;flex-direction:column;gap:16px;">
                @foreach($rankedCharities as $i => $charity)
                    @php
                        $score      = $charity->priority_score;
                        $barWidth   = $maxScore > 0 ? round(($score / $maxScore) * 100) : 0;
                        $severity   = $charity->area_severity ?? 1;
                        $sColor     = $severityColors[$severity] ?? 'var(--muted)';
                        $sLabel     = $severityLabels[$severity] ?? 'Low';
                        $rankColors = ['#C49010','#8C8C8C','#A05A2C'];
                        $rankColor  = $rankColors[$i] ?? 'var(--muted)';
                        $isTop      = $i === 0;
                    @endphp

                    <div style="border:1.5px solid {{ $isTop ? 'rgba(176,40,24,0.25)' : 'rgba(196,144,16,0.12)' }};border-radius:16px;padding:20px 24px;background:{{ $isTop ? 'rgba(176,40,24,0.03)' : 'var(--white)' }};transition:box-shadow 0.2s;"
                         onmouseover="this.style.boxShadow='0 6px 24px rgba(22,8,0,0.08)'"
                         onmouseout="this.style.boxShadow='none'">

                        <div style="display:grid;grid-template-columns:auto 1fr auto;gap:20px;align-items:center;margin-bottom:14px;">

                            {{-- Rank badge --}}
                            <div style="width:40px;height:40px;border-radius:12px;background:{{ $i < 3 ? $rankColor : 'rgba(196,144,16,0.08)' }};display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <span style="font-family:'Cormorant Garamond',serif;font-size:18px;font-weight:700;color:{{ $i < 3 ? 'white' : 'var(--muted)' }};">{{ $i + 1 }}</span>
                            </div>

                            {{-- Name + tags --}}
                            <div>
                                <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:4px;">
                                    <span style="font-family:'Cormorant Garamond',serif;font-size:18px;font-weight:700;color:var(--dark);">
                                        {{ $charity->organization_name ?? $charity->name }}
                                    </span>
                                    @if($charity->is_critical)
                                        <span style="display:inline-flex;align-items:center;gap:5px;background:rgba(176,40,24,0.1);border:1px solid rgba(176,40,24,0.3);color:var(--red);font-size:10px;font-weight:700;letter-spacing:1px;text-transform:uppercase;padding:3px 10px;border-radius:99px;">
                                            <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                            Critical
                                        </span>
                                    @endif
                                    @if($isTop && !$charity->is_critical)
                                        <span style="background:rgba(196,144,16,0.12);color:var(--gold);font-size:10px;font-weight:700;letter-spacing:1px;text-transform:uppercase;padding:3px 10px;border-radius:99px;">Top Priority</span>
                                    @endif
                                </div>
                                <div style="font-size:12px;color:var(--muted);">{{ $charity->address ?? 'No address on file' }}</div>
                            </div>

                            {{-- Score --}}
                            <div style="text-align:right;flex-shrink:0;">
                                <div style="font-family:'Cormorant Garamond',serif;font-size:32px;font-weight:700;color:{{ $isTop ? 'var(--red)' : 'var(--dark)' }};line-height:1;">{{ $score }}</div>
                                <div style="font-size:10px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:var(--muted);">Score</div>
                            </div>

                        </div>

                        {{-- Score bar --}}
                        <div style="height:6px;background:rgba(196,144,16,0.1);border-radius:99px;margin-bottom:14px;overflow:hidden;">
                            <div style="height:100%;width:{{ $barWidth }}%;background:{{ $isTop ? 'linear-gradient(90deg,var(--red),var(--gold))' : 'linear-gradient(90deg,var(--gold),var(--gold2))' }};border-radius:99px;transition:width 0.6s ease;"></div>
                        </div>

                        {{-- Metrics row --}}
                        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;">

                            <div style="padding:12px 16px;background:var(--lighter);border-radius:10px;">
                                <div style="font-size:10px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:var(--muted);margin-bottom:6px;">Area Severity</div>
                                <div style="display:flex;align-items:center;gap:8px;">
                                    <div style="display:flex;gap:3px;">
                                        @for($d = 1; $d <= 4; $d++)
                                            <div style="width:14px;height:14px;border-radius:4px;background:{{ $d <= $severity ? $sColor : 'rgba(196,144,16,0.1)' }};"></div>
                                        @endfor
                                    </div>
                                    <span style="font-size:12px;font-weight:700;color:{{ $sColor }};">{{ $sLabel }}</span>
                                </div>
                            </div>

                            <div style="padding:12px 16px;background:var(--lighter);border-radius:10px;">
                                <div style="font-size:10px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:var(--muted);margin-bottom:6px;">Population</div>
                                <div style="font-size:16px;font-weight:700;color:var(--dark);">
                                    {{ $charity->population_count > 0 ? number_format($charity->population_count) : '—' }}
                                </div>
                            </div>

                            <div style="padding:12px 16px;background:var(--lighter);border-radius:10px;">
                                <div style="font-size:10px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:var(--muted);margin-bottom:6px;">Accessibility</div>
                                <div style="display:flex;align-items:center;gap:8px;">
                                    <div style="flex:1;height:5px;background:rgba(196,144,16,0.1);border-radius:99px;overflow:hidden;">
                                        @php
                                            $acc = $charity->accessibility ?? 100;
                                            $accColor = $acc >= 70 ? '#1A7A40' : ($acc >= 40 ? 'var(--gold)' : 'var(--red)');
                                        @endphp
                                        <div style="height:100%;width:{{ $acc }}%;background:{{ $accColor }};border-radius:99px;"></div>
                                    </div>
                                    <span style="font-size:12px;font-weight:700;color:{{ $accColor }};white-space:nowrap;">{{ $acc }}%</span>
                                </div>
                            </div>

                        </div>

                        {{-- Weather Display --}}
                        @php
                            $weather = $charity->weather ?? [];
                        @endphp
                        @if(!empty($weather))
                            <div style="margin-top:14px;padding:12px 16px;background:linear-gradient(135deg,rgba(59,130,246,0.08),rgba(37,99,235,0.05));border:1px solid rgba(59,130,246,0.15);border-radius:10px;display:flex;align-items:center;gap:16px;">
                                <div style="width:40px;height:40px;background:linear-gradient(135deg,#3B82F6,#2563EB);border-radius:10px;display:flex;align-items:center;justify-content:center;">
                                    @if(isset($weather['icon']))
                                        <img src="https://openweathermap.org/img/wn/{{ $weather['icon'] }}.png" alt="Weather icon" style="width:32px;height:32px;">
                                    @else
                                        <svg width="20" height="20" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24"><path d="M17 18a5 5 0 00-10 0"/><line x1="12" y1="2" x2="12" y2="9"/><line x1="4.22" y1="10.22" x2="5.64" y2="11.64"/><line x1="1" y1="18" x2="3" y2="18"/><line x1="21" y1="18" x2="23" y2="18"/><line x1="18.36" y1="11.64" x2="19.78" y2="10.22"/><line x1="23" y1="22" x2="1" y2="22"/></svg>
                                    @endif
                                </div>
                                <div style="flex:1;">
                                    <div style="font-size:11px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:#2563EB;margin-bottom:2px;">Current Weather</div>
                                    <div style="font-size:14px;font-weight:700;color:var(--dark);">
                                        @if(isset($weather['temperature']))
                                            {{ round($weather['temperature']) }}°C
                                        @endif
                                        @if(isset($weather['condition']))
                                            - {{ $weather['condition'] }}
                                        @endif
                                    </div>
                                </div>
                                <div style="text-align:right;">
                                    <div style="font-size:12px;font-weight:700;color:var(--dark);">{{ $weather['city'] ?? $charity->address ?? 'N/A' }}</div>
                                    @if(isset($weather['humidity']))
                                        <div style="font-size:11px;color:var(--muted);">Humidity: {{ $weather['humidity'] }}%</div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        {{-- Emergency allocation button (only when emergency mode is on) --}}
                        @if($isEmergency && $availableCount > 0 && $i === 0)
                            @php $topDonation = \App\Models\Donation::where('status','available')->orderByDesc('area_severity')->first(); @endphp
                            @if($topDonation)
                            <div style="margin-top:16px;padding-top:16px;border-top:1px solid rgba(196,144,16,0.1);display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;">
                                <div style="font-size:12px;color:var(--muted);">
                                    Suggested allocation: <strong style="color:var(--dark);">{{ Str::limit($topDonation->description, 40) }}</strong>
                                </div>
                                <form method="POST" action="{{ route('admin.confirmAllocation') }}">
                                    @csrf
                                    <input type="hidden" name="donation_id" value="{{ $topDonation->id }}">
                                    <input type="hidden" name="charity_id" value="{{ $charity->id }}">
                                    <button type="submit" class="btn btn-red" style="padding:10px 22px;font-size:12px;">
                                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                                        Confirm Allocation
                                    </button>
                                </form>
                            </div>
                            @endif
                        @endif

                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Emergency Allocation Panel (sorted requests) --}}
    @if($isEmergency && $emergencyData)
        <div style="margin-bottom:14px;"><div class="section-label">Emergency Requests</div></div>
        <div class="card" style="overflow:hidden;margin-bottom:48px;">
            <div style="padding:20px 28px;border-bottom:1px solid rgba(196,144,16,0.1);background:var(--lighter);display:flex;align-items:center;justify-content:space-between;">
                <h3 style="font-family:'Cormorant Garamond',serif;font-size:22px;font-weight:700;color:var(--dark);margin:0;">Open Charity Requests</h3>
                <span style="font-size:12px;color:var(--muted);">Sorted by urgency + area severity</span>
            </div>
            @if($emergencyData['charityRequests']->count() > 0)
                <div style="padding:20px 28px;display:flex;flex-direction:column;gap:10px;">
                    @foreach($emergencyData['charityRequests'] as $req)
                        @php
                            $urgencyColors = ['normal' => 'var(--gold)', 'urgent' => '#F97316', 'critical' => 'var(--red)'];
                            $uColor = $urgencyColors[$req->urgency] ?? 'var(--gold)';
                            $areaSev = $req->charity->area_severity ?? 1;
                        @endphp
                        <div style="display:flex;align-items:center;justify-content:space-between;padding:16px 20px;background:var(--lighter);border-radius:12px;border-left:3px solid {{ $uColor }};">
                            <div>
                                <div style="font-size:14px;font-weight:700;color:var(--dark);margin-bottom:2px;">{{ $req->food_name }}</div>
                                <div style="font-size:12px;color:var(--muted);">{{ $req->charity->organization_name ?? $req->charity->name }}</div>
                            </div>
                            <div style="text-align:right;">
                                <div style="font-size:11px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:{{ $uColor }};">{{ $req->urgency }}</div>
                                <div style="font-size:11px;color:var(--muted);margin-top:2px;">Area Severity: {{ $areaSev }}/4</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div style="padding:48px;text-align:center;font-size:13px;color:var(--muted);">No open charity requests during this emergency period.</div>
            @endif
        </div>
    @endif

    {{-- SDG Reports Section --}}
    @php
        // Get this month's stats
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();
        
        $donationsThisMonth = \App\Models\Donation::withTrashed()
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->count();
            
        $completedDonations = \App\Models\Donation::withTrashed()
            ->whereNotNull('claimed_by')
            ->count();
            
        $totalDonations = \App\Models\Donation::withTrashed()->count();
        $completionRate = $totalDonations > 0 ? round(($completedDonations / $totalDonations) * 100) : 0;
        
        // Most active donors (by number of donations)
        $topDonors = \App\Models\User::where('role', 'donor')
            ->withCount('donations')
            ->orderByDesc('donations_count')
            ->limit(5)
            ->get();
            
        // Most active charities (by number of claimed donations)
        $topCharities = \App\Models\User::where('role', 'charity')
            ->where('verification_status', 'approved')
            ->withCount(['claimedDonations' => function($q) use ($startOfMonth, $endOfMonth) {
                $q->whereBetween('updated_at', [$startOfMonth, $endOfMonth]);
            }])
            ->orderByDesc('claimed_donations_count')
            ->limit(5)
            ->get();
    @endphp
    
    <div style="margin-bottom:14px;">
        <div class="section-label">SDG Reporting</div>
        <h2 style="font-family:'Cormorant Garamond',serif;font-size:32px;font-weight:700;color:var(--dark);margin:0;">Monthly Reports</h2>
    </div>
    
    <div class="admin-stats-grid" style="display:grid;grid-template-columns:repeat(4,1fr);gap:20px;margin-bottom:48px;">
        {{-- Total Donations This Month --}}
        <div class="card" style="padding:24px;text-align:center;">
            <div style="width:48px;height:48px;background:linear-gradient(135deg,var(--red),var(--red2));border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 14px;">
                <svg width="22" height="22" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/></svg>
            </div>
            <div style="font-family:'Cormorant Garamond',serif;font-size:36px;font-weight:700;color:var(--dark);line-height:1;">{{ $donationsThisMonth }}</div>
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:var(--muted);margin-top:8px;">Donations This Month</div>
        </div>
        
        {{-- Completion Rate --}}
        <div class="card" style="padding:24px;text-align:center;">
            <div style="width:48px;height:48px;background:linear-gradient(135deg,var(--gold),var(--gold2));border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 14px;">
                <svg width="22" height="22" fill="none" stroke="var(--dark)" stroke-width="2" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            </div>
            <div style="font-family:'Cormorant Garamond',serif;font-size:36px;font-weight:700;color:var(--dark);line-height:1;">{{ $completionRate }}%</div>
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:var(--muted);margin-top:8px;">Completion Rate</div>
        </div>
        
        {{-- Total Donors --}}
        <div class="card" style="padding:24px;text-align:center;">
            <div style="width:48px;height:48px;background:linear-gradient(135deg,#1A7A40,#2EAD56);border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 14px;">
                <svg width="22" height="22" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
            </div>
            <div style="font-family:'Cormorant Garamond',serif;font-size:36px;font-weight:700;color:var(--dark);line-height:1;">{{ $totalDonors }}</div>
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:var(--muted);margin-top:8px;">Total Donors</div>
        </div>
        
        {{-- Verified Charities --}}
        <div class="card" style="padding:24px;text-align:center;">
            <div style="width:48px;height:48px;background:linear-gradient(135deg,#6366F1,#818CF8);border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 14px;">
                <svg width="22" height="22" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            </div>
            <div style="font-family:'Cormorant Garamond',serif;font-size:36px;font-weight:700;color:var(--dark);line-height:1;">{{ $approvedCount }}</div>
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:var(--muted);margin-top:8px;">Verified Charities</div>
        </div>
    </div>
    
    <div class="admin-donors-charities" style="display:grid;grid-template-columns:1fr 1fr;gap:24px;margin-bottom:48px;">
        {{-- Most Active Donors --}}
        <div class="card" style="padding:0;overflow:hidden;">
            <div style="padding:20px 24px;border-bottom:1px solid rgba(196,144,16,0.1);background:var(--lighter);">
                <h3 style="font-family:'Cormorant Garamond',serif;font-size:20px;font-weight:700;color:var(--dark);margin:0;">Most Active Donors</h3>
                <p style="font-size:12px;color:var(--muted);margin:4px 0 0;">Top donors this month</p>
            </div>
            @if($topDonors->isEmpty())
                <div style="padding:40px;text-align:center;">
                    <p style="font-size:13px;color:var(--muted);">No donors yet</p>
                </div>
            @else
                <div style="padding:16px 20px;display:flex;flex-direction:column;gap:12px;">
                    @foreach($topDonors as $i => $donor)
                        <div style="display:flex;align-items:center;gap:14px;padding:12px 16px;background:var(--lighter);border-radius:10px;">
                            <div style="width:28px;height:28px;background:rgba(196,144,16,0.15);border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:var(--gold);">
                                {{ $i + 1 }}
                            </div>
                            <div style="flex:1;min-width:0;">
                                <div style="font-size:14px;font-weight:700;color:var(--dark);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $donor->name }}</div>
                                <div style="font-size:11px;color:var(--muted);">{{ $donor->donations_count }} {{ Str::plural('donation', $donor->donations_count) }}</div>
                            </div>
                            @if($i === 0)
                                <div style="width:20px;height:20px;background:var(--gold);border-radius:50%;display:flex;align-items:center;justify-content:center;">
                                    <svg width="10" height="10" fill="white" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
        
        {{-- Most Active Charities --}}
        <div class="card" style="padding:0;overflow:hidden;">
            <div style="padding:20px 24px;border-bottom:1px solid rgba(196,144,16,0.1);background:var(--lighter);">
                <h3 style="font-family:'Cormorant Garamond',serif;font-size:20px;font-weight:700;color:var(--dark);margin:0;">Most Active Charities</h3>
                <p style="font-size:12px;color:var(--muted);margin:4px 0 0;">Top charities this month</p>
            </div>
            @if($topCharities->isEmpty())
                <div style="padding:40px;text-align:center;">
                    <p style="font-size:13px;color:var(--muted);">No active charities yet</p>
                </div>
            @else
                <div style="padding:16px 20px;display:flex;flex-direction:column;gap:12px;">
                    @foreach($topCharities as $i => $charity)
                        <div style="display:flex;align-items:center;gap:14px;padding:12px 16px;background:var(--lighter);border-radius:10px;">
                            <div style="width:28px;height:28px;background:rgba(196,144,16,0.15);border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:var(--gold);">
                                {{ $i + 1 }}
                            </div>
                            <div style="flex:1;min-width:0;">
                                <div style="font-size:14px;font-weight:700;color:var(--dark);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $charity->organization_name ?? $charity->name }}</div>
                                <div style="font-size:11px;color:var(--muted);">{{ $charity->claimed_donations_count }} {{ Str::plural('donation', $charity->claimed_donations_count) }} claimed</div>
                            </div>
                            @if($i === 0)
                                <div style="width:20px;height:20px;background:var(--gold);border-radius:50%;display:flex;align-items:center;justify-content:center;">
                                    <svg width="10" height="10" fill="white" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <style>
        @keyframes pulse { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:0.5;transform:scale(1.4)} }
        
        /* Responsive styles */
        @media (max-width: 1024px) {
            .admin-stats-grid { grid-template-columns: repeat(2, 1fr) !important; }
            .admin-donors-charities { grid-template-columns: 1fr !important; }
        }
        
        @media (max-width: 768px) {
            .admin-stats-grid { grid-template-columns: 1fr !important; gap: 12px !important; }
            .admin-stats-grid > div { padding: 16px !important; }
            .admin-donors-charities { grid-template-columns: 1fr !important; }
            .admin-card-full { margin-bottom: 16px; }
        }
        
        @media (max-width: 480px) {
            .admin-stats-grid > div { padding: 14px !important; }
            .admin-stats-grid > div > div:last-child { font-size: 28px !important; }
        }
    </style>

</x-app-layout>