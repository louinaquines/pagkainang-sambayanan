<x-app-layout>
    <x-slot name="header">Dashboard</x-slot>

    @php
        $user          = auth()->user();
        $emergencyMode = $emergencyMode ?? '0';
        $isEmergency   = $emergencyMode == '1';

        $totalDonations = \App\Models\Donation::withTrashed()->count();
        $availableCount = \App\Models\Donation::withTrashed()->where('status','available')->count();
        $completedCount = \App\Models\Donation::withTrashed()->whereNotNull('claimed_by')->count();
        $charityCount   = \App\Models\User::where('role','charity')->where('verification_status','approved')->count();

        $donorUploaded  = \App\Models\Donation::withTrashed()->where('user_id', $user->id)->count();
        $donorCompleted = \App\Models\Donation::withTrashed()->where('user_id', $user->id)->whereNotNull('claimed_by')->count();

        $charityAvailable = \App\Models\Donation::withTrashed()->where('status','available')->count();
        $charityReceived  = \App\Models\Donation::withTrashed()->where('claimed_by', $user->id)->whereNotNull('claimed_by')->count();

        // ── Weather ─────────────────────────────────────────────────────────
        $weatherCity = $user->address ?? 'Mandaue City';
        $weatherCacheKey = 'dashboard_weather_' . md5($weatherCity);

        $weather = \Illuminate\Support\Facades\Cache::remember($weatherCacheKey, 1800, function () use ($weatherCity) {
            try {
                $res = \Illuminate\Support\Facades\Http::timeout(5)
                    ->get('https://api.openweathermap.org/data/2.5/weather', [
                        'q'     => $weatherCity . ',PH',
                        'appid' => config('services.openweather.key'),
                        'units' => 'metric',
                    ]);
                if ($res->successful()) return $res->json();
            } catch (\Exception $e) {}
            return null;
        });

        // ── ReliefWeb ────────────────────────────────────────────────────────
        $disaster = \Illuminate\Support\Facades\Cache::remember('dashboard_reliefweb', 1800, function () {
            try {
                $res = \Illuminate\Support\Facades\Http::timeout(5)
                    ->get('https://api.reliefweb.int/v1/disasters', [
                        'filter[field][country]' => 'Philippines',
                        'limit'  => 1,
                        'sort[]' => 'date:desc',
                        'fields[include][]' => 'name',
                        'fields[include][]' => 'date',
                        'fields[include][]' => 'status',
                        'fields[include][]' => 'type',
                    ]);
                if ($res->successful()) {
                    $items = $res->json()['data'] ?? [];
                    return $items[0]['fields'] ?? null;
                }
            } catch (\Exception $e) {}
            return null;
        });

        $tempNow    = $weather ? round($weather['main']['temp'])       : null;
        $feelsLike  = $weather ? round($weather['main']['feels_like']) : null;
        $humidity   = $weather ? $weather['main']['humidity']          : null;
        $condition  = $weather ? ucfirst($weather['weather'][0]['description']) : 'Unavailable';
        $weatherIcon = $weather ? $weather['weather'][0]['icon'] : null;
        $windSpeed  = $weather ? round($weather['wind']['speed'] * 3.6) : null;

        $disasterName   = $disaster['name']   ?? null;
        $disasterStatus = $disaster['status'] ?? null;
        $disasterDate   = isset($disaster['date']['created'])
            ? \Carbon\Carbon::parse($disaster['date']['created'])->format('M d, Y')
            : null;
        $disasterType = isset($disaster['type'][0]['name']) ? $disaster['type'][0]['name'] : null;

        $weatherSvgMap = [
            '01' => '<svg width="32" height="32" viewBox="0 0 64 64" fill="none"><circle cx="32" cy="32" r="14" fill="#F0C840"/><g stroke="#F0C840" stroke-width="3" stroke-linecap="round"><line x1="32" y1="6" x2="32" y2="14"/><line x1="32" y1="50" x2="32" y2="58"/><line x1="6" y1="32" x2="14" y2="32"/><line x1="50" y1="32" x2="58" y2="32"/><line x1="14" y1="14" x2="20" y2="20"/><line x1="44" y1="44" x2="50" y2="50"/><line x1="50" y1="14" x2="44" y2="20"/><line x1="20" y1="44" x2="14" y2="50"/></g></svg>',
            '02' => '<svg width="32" height="32" viewBox="0 0 64 64" fill="none"><circle cx="24" cy="28" r="10" fill="#F0C840" opacity="0.9"/><ellipse cx="36" cy="36" rx="16" ry="12" fill="rgba(255,255,255,0.85)"/><ellipse cx="22" cy="38" rx="10" ry="8" fill="rgba(255,255,255,0.75)"/></svg>',
            '03' => '<svg width="32" height="32" viewBox="0 0 64 64" fill="none"><ellipse cx="36" cy="34" rx="18" ry="13" fill="rgba(255,255,255,0.7)"/><ellipse cx="22" cy="37" rx="12" ry="9" fill="rgba(255,255,255,0.6)"/></svg>',
            '04' => '<svg width="32" height="32" viewBox="0 0 64 64" fill="none"><ellipse cx="34" cy="30" rx="16" ry="11" fill="rgba(255,255,255,0.45)"/><ellipse cx="20" cy="36" rx="14" ry="10" fill="rgba(255,255,255,0.55)"/></svg>',
            '09' => '<svg width="32" height="32" viewBox="0 0 64 64" fill="none"><ellipse cx="32" cy="24" rx="18" ry="12" fill="rgba(255,255,255,0.5)"/><g stroke="rgba(100,180,255,0.9)" stroke-width="2.5" stroke-linecap="round"><line x1="24" y1="40" x2="20" y2="52"/><line x1="32" y1="40" x2="28" y2="52"/><line x1="40" y1="40" x2="36" y2="52"/></g></svg>',
            '10' => '<svg width="32" height="32" viewBox="0 0 64 64" fill="none"><circle cx="22" cy="22" r="9" fill="#F0C840" opacity="0.7"/><ellipse cx="34" cy="28" rx="16" ry="11" fill="rgba(255,255,255,0.55)"/><g stroke="rgba(100,180,255,0.9)" stroke-width="2.5" stroke-linecap="round"><line x1="28" y1="42" x2="24" y2="54"/><line x1="36" y1="42" x2="32" y2="54"/></g></svg>',
            '11' => '<svg width="32" height="32" viewBox="0 0 64 64" fill="none"><ellipse cx="32" cy="22" rx="18" ry="12" fill="rgba(255,255,255,0.4)"/><polyline points="36,34 28,46 34,46 26,58" stroke="#F0C840" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" fill="none"/></svg>',
            '13' => '<svg width="32" height="32" viewBox="0 0 64 64" fill="none"><ellipse cx="32" cy="24" rx="18" ry="12" fill="rgba(255,255,255,0.5)"/><g fill="rgba(255,255,255,0.9)"><circle cx="24" cy="42" r="3"/><circle cx="32" cy="46" r="3"/><circle cx="40" cy="42" r="3"/><circle cx="28" cy="52" r="3"/><circle cx="36" cy="52" r="3"/></g></svg>',
            '50' => '<svg width="32" height="32" viewBox="0 0 64 64" fill="none"><g stroke="rgba(255,255,255,0.45)" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="28" x2="52" y2="28"/><line x1="12" y1="36" x2="52" y2="36"/><line x1="12" y1="44" x2="52" y2="44"/></g></svg>',
        ];
        $iconPrefix = $weatherIcon ? substr($weatherIcon, 0, 2) : '01';
        $weatherSvg = $weatherSvgMap[$iconPrefix] ?? $weatherSvgMap['01'];

        $disasterColor = match($disasterStatus) {
            'alert'   => '#E04030',
            'ongoing' => '#DCA820',
            default   => 'rgba(255,255,255,0.35)',
        };
    @endphp

    {{-- ═══ HERO SECTION ═══ --}}
    <div style="background:linear-gradient(145deg,var(--dark) 0%,#2A0E04 45%,#3C1A08 100%);border-radius:20px;overflow:hidden;margin-bottom:48px;box-shadow:0 20px 60px rgba(22,8,0,0.3),0 4px 16px rgba(22,8,0,0.2);position:relative;">
        <div style="position:absolute;inset:0;overflow:hidden;pointer-events:none;">
            <div style="position:absolute;top:-100px;right:-100px;width:400px;height:400px;border-radius:50%;background:radial-gradient(circle,rgba(196,144,16,0.12) 0%,transparent 65%);"></div>
            <div style="position:absolute;bottom:-80px;left:30%;width:300px;height:300px;border-radius:50%;background:radial-gradient(circle,rgba(176,40,24,0.1) 0%,transparent 65%);"></div>
            <div style="position:absolute;top:0;left:0;right:0;bottom:0;background:linear-gradient(115deg,rgba(255,255,255,0.02) 0%,transparent 40%);"></div>
            <div style="position:absolute;top:0;left:0;bottom:0;width:4px;background:linear-gradient(180deg,var(--red),var(--gold),var(--red));"></div>
        </div>

        <div class="hero-grid">

            {{-- LEFT: Welcome --}}
            <div class="hero-left">
                <div class="section-label" style="color:var(--gold3);margin-bottom:16px;">
                    <span style="background:var(--gold3);"></span>
                    Welcome back
                </div>
                <h1 class="hero-name">{{ $user->name }}</h1>
                <p style="font-size:15px;color:rgba(255,255,255,0.5);line-height:1.8;max-width:480px;margin-bottom:28px;">
                    Together we bridge surplus food with communities in need across the Philippines.
                </p>
                <div class="hero-actions">
                    <div style="display:inline-flex;align-items:center;gap:8px;border:1px solid rgba(255,255,255,0.15);padding:8px 18px;border-radius:99px;">
                        <div style="width:7px;height:7px;border-radius:50%;background:var(--gold3);"></div>
                        <span style="font-size:12px;font-weight:600;color:rgba(255,255,255,0.75);text-transform:capitalize;letter-spacing:0.5px;">{{ $user->role }}</span>
                    </div>
                    @if($user->role === 'charity')
                        <div style="display:inline-flex;align-items:center;gap:8px;border:1px solid rgba(255,255,255,0.1);padding:8px 18px;border-radius:99px;">
                            <div style="width:7px;height:7px;border-radius:50%;background:{{ $user->verification_status === 'approved' ? '#25A855' : '#DCA820' }};"></div>
                            <span style="font-size:12px;font-weight:600;color:rgba(255,255,255,0.6);text-transform:capitalize;">{{ $user->verification_status }}</span>
                        </div>
                    @endif
                    @if($user->role === 'donor')
                        <a href="{{ route('donations.create') }}" class="btn btn-gold" style="padding:9px 22px;font-size:11px;">Post a Donation <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg></a>
                    @elseif($user->role === 'charity' && $user->verification_status === 'approved')
                        <a href="{{ route('charity.request.create') }}" class="btn btn-gold" style="padding:9px 22px;font-size:11px;">Post a Request <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg></a>
                        <button type="button" onclick="openEmergencyModal()" class="btn btn-outline" style="padding:9px 18px;font-size:11px;border-color:rgba(255,255,255,0.3);color:white;background-color:var(--green);">Update Status <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg></button>
                    @endif
                </div>
            </div>

            {{-- RIGHT: Weather + Disaster Widget --}}
            <div class="hero-right">

                {{-- Weather --}}
                <div class="hero-weather">
                    <div style="position:absolute;top:-30px;right:-30px;width:140px;height:140px;border-radius:50%;background:radial-gradient(circle,rgba(196,144,16,0.1) 0%,transparent 70%);pointer-events:none;"></div>
                    <div style="font-size:10px;font-weight:700;letter-spacing:2.5px;text-transform:uppercase;color:rgba(196,144,16,0.6);margin-bottom:16px;">Current Weather · {{ $weatherCity }}</div>

                    @if($weather)
                        <div style="display:flex;align-items:center;gap:18px;margin-bottom:20px;">
                            <div style="flex-shrink:0;">{!! $weatherSvg !!}</div>
                            <div>
                                <div class="weather-temp">{{ $tempNow }}°<span style="font-size:22px;color:rgba(255,255,255,0.4);">C</span></div>
                                <div style="font-size:13px;color:rgba(255,255,255,0.55);margin-top:2px;">{{ $condition }}</div>
                            </div>
                        </div>
                        <div class="weather-mini-grid">
                            <div style="background:rgba(255,255,255,0.05);border-radius:10px;padding:10px 12px;border:1px solid rgba(255,255,255,0.06);">
                                <div style="font-size:10px;color:rgba(255,255,255,0.35);letter-spacing:1px;text-transform:uppercase;margin-bottom:4px;">Feels</div>
                                <div style="font-size:15px;font-weight:700;color:rgba(255,255,255,0.8);">{{ $feelsLike }}°C</div>
                            </div>
                            <div style="background:rgba(255,255,255,0.05);border-radius:10px;padding:10px 12px;border:1px solid rgba(255,255,255,0.06);">
                                <div style="font-size:10px;color:rgba(255,255,255,0.35);letter-spacing:1px;text-transform:uppercase;margin-bottom:4px;">Humidity</div>
                                <div style="font-size:15px;font-weight:700;color:rgba(255,255,255,0.8);">{{ $humidity }}%</div>
                            </div>
                            <div style="background:rgba(255,255,255,0.05);border-radius:10px;padding:10px 12px;border:1px solid rgba(255,255,255,0.06);">
                                <div style="font-size:10px;color:rgba(255,255,255,0.35);letter-spacing:1px;text-transform:uppercase;margin-bottom:4px;">Wind</div>
                                <div style="font-size:15px;font-weight:700;color:rgba(255,255,255,0.8);">{{ $windSpeed }} km/h</div>
                            </div>
                        </div>
                    @else
                        <div style="display:flex;align-items:center;gap:12px;padding:18px;background:rgba(255,255,255,0.04);border-radius:12px;border:1px solid rgba(255,255,255,0.07);">
                            <svg width="20" height="20" fill="none" stroke="rgba(255,255,255,0.3)" stroke-width="1.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            <span style="font-size:13px;color:rgba(255,255,255,0.3);">Weather data unavailable</span>
                        </div>
                    @endif
                </div>

                {{-- Divider --}}
                <div style="height:1px;background:rgba(255,255,255,0.07);margin:0 40px;"></div>

                {{-- Disaster --}}
                <div class="hero-disaster">
                    <div style="font-size:10px;font-weight:700;letter-spacing:2.5px;text-transform:uppercase;color:rgba(196,144,16,0.6);margin-bottom:14px;">Latest PH Disaster Alert</div>
                    @if($disaster)
                        <div style="display:flex;align-items:flex-start;gap:12px;">
                            <div style="flex-shrink:0;width:8px;height:8px;border-radius:50%;background:{{ $disasterColor }};margin-top:5px;{{ $disasterStatus === 'alert' ? 'box-shadow:0 0 8px rgba(224,64,48,0.8);animation:pulse 1.5s ease infinite;' : '' }}"></div>
                            <div style="flex:1;min-width:0;">
                                <div style="font-size:13px;font-weight:600;color:rgba(255,255,255,0.85);line-height:1.4;margin-bottom:6px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;" title="{{ $disasterName }}">{{ $disasterName }}</div>
                                <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                                    @if($disasterType)<span style="font-size:10px;font-weight:700;letter-spacing:0.8px;text-transform:uppercase;color:rgba(255,255,255,0.35);">{{ $disasterType }}</span><span style="width:3px;height:3px;border-radius:50%;background:rgba(255,255,255,0.2);"></span>@endif
                                    @if($disasterDate)<span style="font-size:10px;color:rgba(255,255,255,0.3);">{{ $disasterDate }}</span>@endif
                                    @if($disasterStatus)<span style="font-size:10px;font-weight:700;letter-spacing:0.8px;text-transform:uppercase;color:{{ $disasterColor }};padding:2px 8px;border-radius:99px;background:{{ $disasterStatus === 'alert' ? 'rgba(224,64,48,0.15)' : 'rgba(255,255,255,0.05)' }};border:1px solid {{ $disasterStatus === 'alert' ? 'rgba(224,64,48,0.3)' : 'rgba(255,255,255,0.1)' }};">{{ $disasterStatus }}</span>@endif
                                </div>
                            </div>
                        </div>
                    @else
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:8px;height:8px;border-radius:50%;background:rgba(255,255,255,0.15);"></div>
                            <span style="font-size:13px;color:rgba(255,255,255,0.25);">No active alerts</span>
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>

    {{-- ═══ STAT CARDS ═══ --}}
    <div style="margin-bottom:14px;"><div class="section-label">Overview</div></div>
    <div class="stats-grid stagger">
        @if($user->role === 'donor')
            <div class="stat-card"><div style="width:40px;height:40px;background:linear-gradient(135deg,rgba(176,40,24,0.1),rgba(176,40,24,0.2));border-radius:10px;display:flex;align-items:center;justify-content:center;margin:0 auto 18px;"><svg width="18" height="18" fill="none" stroke="var(--red)" stroke-width="1.8" viewBox="0 0 24 24"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v16"/></svg></div><div class="stat-num" style="color:var(--red);">{{ $donorUploaded }}</div><div class="stat-lbl">My Donations</div></div>
            <div class="stat-card"><div style="width:40px;height:40px;background:linear-gradient(135deg,rgba(26,90,168,0.1),rgba(26,90,168,0.2));border-radius:10px;display:flex;align-items:center;justify-content:center;margin:0 auto 18px;"><svg width="18" height="18" fill="none" stroke="#1A5AA8" stroke-width="1.8" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg></div><div class="stat-num" style="color:#1A5AA8;">{{ $availableCount }}</div><div class="stat-lbl">Available Now</div></div>
            <div class="stat-card"><div style="width:40px;height:40px;background:linear-gradient(135deg,rgba(26,122,64,0.1),rgba(26,122,64,0.2));border-radius:10px;display:flex;align-items:center;justify-content:center;margin:0 auto 18px;"><svg width="18" height="18" fill="none" stroke="#1A7A40" stroke-width="1.8" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg></div><div class="stat-num" style="color:#1A7A40;">{{ $donorCompleted }}</div><div class="stat-lbl">Completed</div></div>
            <div class="stat-card"><div style="width:40px;height:40px;background:linear-gradient(135deg,rgba(196,144,16,0.1),rgba(196,144,16,0.2));border-radius:10px;display:flex;align-items:center;justify-content:center;margin:0 auto 18px;"><svg width="18" height="18" fill="none" stroke="var(--gold)" stroke-width="1.8" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg></div><div class="stat-num" style="color:var(--gold);">{{ $charityCount }}</div><div class="stat-lbl">Verified Charities</div></div>
        @elseif($user->role === 'charity')
            <div class="stat-card"><div style="width:40px;height:40px;background:linear-gradient(135deg,rgba(26,90,168,0.1),rgba(26,90,168,0.2));border-radius:10px;display:flex;align-items:center;justify-content:center;margin:0 auto 18px;"><svg width="18" height="18" fill="none" stroke="#1A5AA8" stroke-width="1.8" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg></div><div class="stat-num" style="color:#1A5AA8;">{{ $charityAvailable }}</div><div class="stat-lbl">Available Now</div></div>
            <div class="stat-card"><div style="width:40px;height:40px;background:linear-gradient(135deg,rgba(26,122,64,0.1),rgba(26,122,64,0.2));border-radius:10px;display:flex;align-items:center;justify-content:center;margin:0 auto 18px;"><svg width="18" height="18" fill="none" stroke="#1A7A40" stroke-width="1.8" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg></div><div class="stat-num" style="color:#1A7A40;">{{ $charityReceived }}</div><div class="stat-lbl">Completed</div></div>
            <div class="stat-card"><div style="width:40px;height:40px;background:linear-gradient(135deg,rgba(176,40,24,0.1),rgba(176,40,24,0.2));border-radius:10px;display:flex;align-items:center;justify-content:center;margin:0 auto 18px;"><svg width="18" height="18" fill="none" stroke="var(--red)" stroke-width="1.8" viewBox="0 0 24 24"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v16"/></svg></div><div class="stat-num" style="color:var(--red);">{{ $totalDonations }}</div><div class="stat-lbl">Total Donations</div></div>
            <div class="stat-card"><div style="width:40px;height:40px;background:linear-gradient(135deg,rgba(196,144,16,0.1),rgba(196,144,16,0.2));border-radius:10px;display:flex;align-items:center;justify-content:center;margin:0 auto 18px;"><svg width="18" height="18" fill="none" stroke="var(--gold)" stroke-width="1.8" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg></div><div class="stat-num" style="color:var(--gold);">{{ $charityCount }}</div><div class="stat-lbl">Verified Charities</div></div>
        @else
            <div class="stat-card"><div style="width:40px;height:40px;background:linear-gradient(135deg,rgba(176,40,24,0.1),rgba(176,40,24,0.2));border-radius:10px;display:flex;align-items:center;justify-content:center;margin:0 auto 18px;"><svg width="18" height="18" fill="none" stroke="var(--red)" stroke-width="1.8" viewBox="0 0 24 24"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v16"/></svg></div><div class="stat-num" style="color:var(--red);">{{ $totalDonations }}</div><div class="stat-lbl">Total Donations</div></div>
            <div class="stat-card"><div style="width:40px;height:40px;background:linear-gradient(135deg,rgba(26,90,168,0.1),rgba(26,90,168,0.2));border-radius:10px;display:flex;align-items:center;justify-content:center;margin:0 auto 18px;"><svg width="18" height="18" fill="none" stroke="#1A5AA8" stroke-width="1.8" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg></div><div class="stat-num" style="color:#1A5AA8;">{{ $availableCount }}</div><div class="stat-lbl">Available Now</div></div>
            <div class="stat-card"><div style="width:40px;height:40px;background:linear-gradient(135deg,rgba(26,122,64,0.1),rgba(26,122,64,0.2));border-radius:10px;display:flex;align-items:center;justify-content:center;margin:0 auto 18px;"><svg width="18" height="18" fill="none" stroke="#1A7A40" stroke-width="1.8" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg></div><div class="stat-num" style="color:#1A7A40;">{{ $completedCount }}</div><div class="stat-lbl">Completed</div></div>
            <div class="stat-card"><div style="width:40px;height:40px;background:linear-gradient(135deg,rgba(196,144,16,0.1),rgba(196,144,16,0.2));border-radius:10px;display:flex;align-items:center;justify-content:center;margin:0 auto 18px;"><svg width="18" height="18" fill="none" stroke="var(--gold)" stroke-width="1.8" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg></div><div class="stat-num" style="color:var(--gold);">{{ $charityCount }}</div><div class="stat-lbl">Verified Charities</div></div>
        @endif
    </div>

    {{-- ═══ QUICK ACTIONS ═══ --}}
    <div style="margin-bottom:14px;"><div class="section-label">Actions</div></div>
    <div class="actions-grid">

        <div class="card" style="padding:36px;">
            <div style="margin-bottom:28px;">
                <div class="section-label">Quick Actions</div>
                <h3 style="font-family:'Cormorant Garamond',serif;font-size:26px;font-weight:700;color:var(--dark);">What would you like to do?</h3>
            </div>
            <div class="actions-inner-grid">
                @if($user->role === 'donor')
                    <a href="{{ route('donations.create') }}" class="action-btn" style="background:linear-gradient(135deg,var(--red),var(--red2));box-shadow:0 4px 16px rgba(176,40,24,0.25);"><div style="position:absolute;top:-20px;right:-20px;width:80px;height:80px;border-radius:50%;background:rgba(255,255,255,0.08);"></div><div style="width:44px;height:44px;background:rgba(255,255,255,0.15);border-radius:12px;display:flex;align-items:center;justify-content:center;"><svg width="20" height="20" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></div><div><div style="font-size:11px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:rgba(255,255,255,0.6);margin-bottom:4px;">Donate</div><div style="font-size:16px;font-weight:700;color:white;font-family:'Cormorant Garamond',serif;">Post a Donation</div></div></a>
                    <a href="{{ route('charity.requests.index') }}" class="action-btn" style="background:linear-gradient(135deg,var(--gold),var(--gold2));box-shadow:0 4px 16px rgba(196,144,16,0.25);"><div style="position:absolute;top:-20px;right:-20px;width:80px;height:80px;border-radius:50%;background:rgba(255,255,255,0.1);"></div><div style="width:44px;height:44px;background:rgba(255,255,255,0.2);border-radius:12px;display:flex;align-items:center;justify-content:center;"><svg width="20" height="20" fill="none" stroke="var(--dark)" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg></div><div><div style="font-size:11px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:rgba(22,8,0,0.45);margin-bottom:4px;">Browse</div><div style="font-size:16px;font-weight:700;color:var(--dark);font-family:'Cormorant Garamond',serif;">Charity Requests</div></div></a>
                    <a href="{{ route('donations.available') }}" class="action-btn" style="background:linear-gradient(135deg,#1A5AA8,#2266C0);box-shadow:0 4px 16px rgba(26,90,168,0.25);"><div style="position:absolute;top:-20px;right:-20px;width:80px;height:80px;border-radius:50%;background:rgba(255,255,255,0.08);"></div><div style="width:44px;height:44px;background:rgba(255,255,255,0.15);border-radius:12px;display:flex;align-items:center;justify-content:center;"><svg width="20" height="20" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg></div><div><div style="font-size:11px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:rgba(255,255,255,0.6);margin-bottom:4px;">Browse</div><div style="font-size:16px;font-weight:700;color:white;font-family:'Cormorant Garamond',serif;">Available Food</div></div></a>
                    <a href="{{ route('feedback.index') }}" class="action-btn" style="background:linear-gradient(135deg,#1A7A40,#25A855);box-shadow:0 4px 16px rgba(26,122,64,0.25);"><div style="position:absolute;top:-20px;right:-20px;width:80px;height:80px;border-radius:50%;background:rgba(255,255,255,0.08);"></div><div style="width:44px;height:44px;background:rgba(255,255,255,0.15);border-radius:12px;display:flex;align-items:center;justify-content:center;"><svg width="20" height="20" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg></div><div><div style="font-size:11px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:rgba(255,255,255,0.6);margin-bottom:4px;">Review</div><div style="font-size:16px;font-weight:700;color:white;font-family:'Cormorant Garamond',serif;">Feedback Log</div></div></a>
                @elseif($user->role === 'charity')
                    @if($user->verification_status === 'approved')
                    <a href="{{ route('charity.request.create') }}" class="action-btn" style="background:linear-gradient(135deg,var(--red),var(--red2));box-shadow:0 4px 16px rgba(176,40,24,0.25);"><div style="position:absolute;top:-20px;right:-20px;width:80px;height:80px;border-radius:50%;background:rgba(255,255,255,0.08);"></div><div style="width:44px;height:44px;background:rgba(255,255,255,0.15);border-radius:12px;display:flex;align-items:center;justify-content:center;"><svg width="20" height="20" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></div><div><div style="font-size:11px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:rgba(255,255,255,0.6);margin-bottom:4px;">Request</div><div style="font-size:16px;font-weight:700;color:white;font-family:'Cormorant Garamond',serif;">Post a Request</div></div></a>
                    @endif
                    <a href="{{ route('donations.available') }}" class="action-btn" style="background:linear-gradient(135deg,var(--gold),var(--gold2));box-shadow:0 4px 16px rgba(196,144,16,0.25);"><div style="position:absolute;top:-20px;right:-20px;width:80px;height:80px;border-radius:50%;background:rgba(255,255,255,0.1);"></div><div style="width:44px;height:44px;background:rgba(255,255,255,0.2);border-radius:12px;display:flex;align-items:center;justify-content:center;"><svg width="20" height="20" fill="none" stroke="var(--dark)" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg></div><div><div style="font-size:11px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:rgba(22,8,0,0.45);margin-bottom:4px;">Browse</div><div style="font-size:16px;font-weight:700;color:var(--dark);font-family:'Cormorant Garamond',serif;">Available Food</div></div></a>
                    <a href="{{ route('donations.index') }}" class="action-btn" style="background:linear-gradient(135deg,#1A7A40,#25A855);box-shadow:0 4px 16px rgba(26,122,64,0.25);"><div style="position:absolute;top:-20px;right:-20px;width:80px;height:80px;border-radius:50%;background:rgba(255,255,255,0.08);"></div><div style="width:44px;height:44px;background:rgba(255,255,255,0.15);border-radius:12px;display:flex;align-items:center;justify-content:center;"><svg width="20" height="20" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4L16.5 3.5z"/></svg></div><div><div style="font-size:11px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:rgba(255,255,255,0.6);margin-bottom:4px;">Records</div><div style="font-size:16px;font-weight:700;color:white;font-family:'Cormorant Garamond',serif;">Donation History</div></div></a>
                    <div class="action-btn" style="background:var(--white);border:1.5px dashed rgba(196,144,16,0.2);box-shadow:none;cursor:default;"><div style="width:44px;height:44px;background:rgba(196,144,16,0.08);border-radius:12px;display:flex;align-items:center;justify-content:center;"><svg width="20" height="20" fill="none" stroke="var(--gold)" stroke-width="1.5" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div><div><div style="font-size:11px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:var(--muted);margin-bottom:4px;">Status</div><div style="font-size:16px;font-weight:700;color:var(--dark);font-family:'Cormorant Garamond',serif;">{{ $user->verification_status === 'approved' ? 'Verified Charity' : 'Pending Verification' }}</div></div></div>
                @elseif($user->role === 'admin')
                    <a href="{{ route('donations.available') }}" class="action-btn" style="background:linear-gradient(135deg,var(--gold),var(--gold2));box-shadow:0 4px 16px rgba(196,144,16,0.25);"><div style="position:absolute;top:-20px;right:-20px;width:80px;height:80px;border-radius:50%;background:rgba(255,255,255,0.1);"></div><div style="width:44px;height:44px;background:rgba(255,255,255,0.2);border-radius:12px;display:flex;align-items:center;justify-content:center;"><svg width="20" height="20" fill="none" stroke="var(--dark)" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg></div><div><div style="font-size:11px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:rgba(22,8,0,0.45);margin-bottom:4px;">Browse</div><div style="font-size:16px;font-weight:700;color:var(--dark);font-family:'Cormorant Garamond',serif;">Available Food</div></div></a>
                    <a href="{{ route('feedback.index') }}" class="action-btn" style="background:linear-gradient(135deg,#1A7A40,#25A855);box-shadow:0 4px 16px rgba(26,122,64,0.25);"><div style="position:absolute;top:-20px;right:-20px;width:80px;height:80px;border-radius:50%;background:rgba(255,255,255,0.08);"></div><div style="width:44px;height:44px;background:rgba(255,255,255,0.15);border-radius:12px;display:flex;align-items:center;justify-content:center;"><svg width="20" height="20" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg></div><div><div style="font-size:11px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:rgba(255,255,255,0.6);margin-bottom:4px;">Review</div><div style="font-size:16px;font-weight:700;color:white;font-family:'Cormorant Garamond',serif;">Feedback Log</div></div></a>
                    <a href="{{ route('donations.index') }}" class="action-btn" style="background:linear-gradient(135deg,#1A5AA8,#2266C0);box-shadow:0 4px 16px rgba(26,90,168,0.25);"><div style="position:absolute;top:-20px;right:-20px;width:80px;height:80px;border-radius:50%;background:rgba(255,255,255,0.08);"></div><div style="width:44px;height:44px;background:rgba(255,255,255,0.15);border-radius:12px;display:flex;align-items:center;justify-content:center;"><svg width="20" height="20" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4L16.5 3.5z"/></svg></div><div><div style="font-size:11px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:rgba(255,255,255,0.6);margin-bottom:4px;">Records</div><div style="font-size:16px;font-weight:700;color:white;font-family:'Cormorant Garamond',serif;">Donation History</div></div></a>
                    <a href="{{ route('admin.charities') }}" class="action-btn" style="background:var(--white);border:1.5px solid rgba(196,144,16,0.2);box-shadow:0 2px 10px rgba(22,8,0,0.05);" onmouseover="this.style.borderColor='var(--gold)'" onmouseout="this.style.borderColor='rgba(196,144,16,0.2)'"><div style="width:44px;height:44px;background:rgba(196,144,16,0.1);border-radius:12px;display:flex;align-items:center;justify-content:center;"><svg width="20" height="20" fill="none" stroke="var(--gold)" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg></div><div><div style="font-size:11px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:var(--muted);margin-bottom:4px;">Admin</div><div style="font-size:16px;font-weight:700;color:var(--dark);font-family:'Cormorant Garamond',serif;">Manage Charities</div></div></a>
                @endif
            </div>
        </div>

        @if($user->role === 'admin')
        <div class="card" style="padding:36px;border-top:3px solid {{ $isEmergency ? 'var(--red)' : 'var(--gold)' }};">
            <div style="display:flex;align-items:flex-start;gap:16px;margin-bottom:20px;">
                <div style="width:52px;height:52px;flex-shrink:0;border-radius:14px;background:{{ $isEmergency ? 'linear-gradient(135deg,var(--red),var(--red2))' : 'linear-gradient(135deg,var(--gold),var(--gold2))' }};display:flex;align-items:center;justify-content:center;box-shadow:0 6px 20px {{ $isEmergency ? 'rgba(176,40,24,0.35)' : 'rgba(196,144,16,0.35)' }};"><svg width="22" height="22" fill="none" stroke="{{ $isEmergency ? 'white' : 'var(--dark)' }}" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg></div>
                <div>
                    <h3 style="font-family:'Cormorant Garamond',serif;font-size:24px;font-weight:700;color:var(--dark);margin-bottom:5px;">Emergency Mode</h3>
                    <div style="display:inline-flex;align-items:center;gap:7px;">
                        <div style="width:7px;height:7px;border-radius:50%;background:{{ $isEmergency ? 'var(--red)' : 'var(--muted)' }};{{ $isEmergency ? 'box-shadow:0 0 8px rgba(176,40,24,0.7);animation:pulse 1.5s ease infinite;' : '' }}"></div>
                        <span style="font-size:11px;font-weight:700;letter-spacing:1.2px;text-transform:uppercase;color:{{ $isEmergency ? 'var(--red)' : 'var(--muted)' }};">{{ $isEmergency ? 'Currently Active' : 'Inactive' }}</span>
                    </div>
                </div>
            </div>
            <p style="font-size:13px;color:var(--muted);line-height:1.8;margin-bottom:24px;padding-left:14px;border-left:2px solid rgba(196,144,16,0.2);">When active, the platform prioritizes all incoming donations for disaster-affected communities across the Philippines.</p>
            <form method="POST" action="{{ route('admin.toggleEmergency') }}">
                @csrf
                <button type="submit" class="btn {{ $isEmergency ? 'btn-green' : 'btn-red' }}" style="width:100%;padding:14px;justify-content:space-between;">
                    <span>{{ $isEmergency ? 'Deactivate Emergency Mode' : 'Activate Emergency Mode' }}</span>
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">@if($isEmergency)<polyline points="20 6 9 17 4 12"/>@else<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>@endif</svg>
                </button>
            </form>
        </div>
        @endif

    </div>

    {{-- ═══ SDG BANNER ═══ --}}
    <div class="sdg-banner">
        <div class="sdg-banner-icon">
            <svg width="26" height="26" fill="none" stroke="var(--dark)" stroke-width="1.8" viewBox="0 0 24 24"><path d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
        </div>
        <div>
            <div class="section-label" style="margin-bottom:6px;">Our Commitment</div>
            <h3 style="font-family:'Cormorant Garamond',serif;font-size:22px;font-weight:700;color:var(--dark);margin-bottom:6px;">SDG No. 2: Zero Hunger</h3>
            <p style="font-size:13px;color:var(--muted);line-height:1.7;max-width:580px;">Pagkainang Sambayanan connects food donors with communities in need — working toward a Philippines where no one goes to bed hungry.</p>
        </div>
        @if($user->role === 'donor')
            <a href="{{ route('donations.create') }}" class="btn btn-dark" style="white-space:nowrap;">Start Donating <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg></a>
        @elseif($user->role === 'charity' && $user->verification_status === 'approved')
            <a href="{{ route('charity.request.create') }}" class="btn btn-dark" style="white-space:nowrap;">Post a Request <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg></a>
        @else
            <a href="{{ route('donations.available') }}" class="btn btn-dark" style="white-space:nowrap;">View Donations <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg></a>
        @endif
    </div>

    <style>
        @keyframes pulse { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:0.5;transform:scale(1.4)} }

        /* ── Hero ── */
        .hero-grid {
            position: relative;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0;
            align-items: stretch;
        }
        .hero-left {
            padding: 52px 48px 52px 56px;
        }
        .hero-name {
            font-family: 'Cormorant Garamond', serif;
            font-size: clamp(28px, 4vw, 56px);
            font-weight: 700;
            color: white;
            line-height: 1.05;
            letter-spacing: -1px;
            margin-bottom: 16px;
        }
        .hero-actions {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }
        .hero-right {
            border-left: 1px solid rgba(255,255,255,0.07);
            display: flex;
            flex-direction: column;
        }
        .hero-weather {
            flex: 1;
            padding: 36px 40px 24px;
            position: relative;
            overflow: hidden;
        }
        .weather-temp {
            font-family: 'Cormorant Garamond', serif;
            font-size: 52px;
            font-weight: 700;
            color: white;
            line-height: 1;
        }
        .weather-mini-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 10px;
        }
        .hero-disaster {
            padding: 20px 40px 32px;
        }

        /* ── Stat cards ── */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 48px;
        }

        /* ── Actions ── */
        .actions-grid {
            display: grid;
            grid-template-columns: {{ $user->role === 'admin' ? '1fr 1fr' : '1fr' }};
            gap: 24px;
            margin-bottom: 48px;
        }
        .actions-inner-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 14px;
        }
        .action-btn {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 12px;
            padding: 24px;
            border-radius: 14px;
            text-decoration: none;
            transition: all 0.25s ease;
            position: relative;
            overflow: hidden;
            border: none;
        }
        .action-btn:hover { transform: translateY(-4px); }

        /* ── SDG Banner ── */
        .sdg-banner {
            display: grid;
            grid-template-columns: auto 1fr auto;
            align-items: center;
            gap: 32px;
            background: var(--white);
            border: 1px solid rgba(196,144,16,0.18);
            border-radius: 16px;
            padding: 32px 36px;
            box-shadow: 0 2px 16px rgba(22,8,0,0.04);
        }
        .sdg-banner-icon {
            width: 56px;
            height: 56px;
            background: linear-gradient(135deg, var(--gold), var(--gold2));
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 6px 20px rgba(196,144,16,0.3);
            flex-shrink: 0;
        }

        /* ── Responsive ── */
        @media (max-width: 1024px) {
            .hero-grid { grid-template-columns: 1fr !important; }
            .hero-right { border-left: none !important; border-top: 1px solid rgba(255,255,255,0.07); }
            .stats-grid { grid-template-columns: repeat(2, 1fr) !important; }
            .actions-grid { grid-template-columns: 1fr !important; }
            .sdg-banner { grid-template-columns: auto 1fr; }
            .sdg-banner .btn { grid-column: 1 / -1; justify-self: start; }
        }

        @media (max-width: 768px) {
            .hero-left { padding: 28px 20px 24px !important; }
            .hero-weather { padding: 24px 20px 18px !important; }
            .hero-disaster { padding: 16px 20px 24px !important; }
            .weather-mini-grid { grid-template-columns: 1fr 1fr !important; }
            .weather-temp { font-size: 40px !important; }
            .stats-grid { grid-template-columns: repeat(2, 1fr) !important; gap: 12px !important; }
            .stat-card { padding: 22px 14px !important; }
            .stat-num { font-size: 38px !important; }
            .actions-inner-grid { grid-template-columns: 1fr !important; }
            .sdg-banner { grid-template-columns: 1fr !important; gap: 16px !important; padding: 24px 20px !important; }
            .sdg-banner-icon { display: none; }
        }

        @media (max-width: 480px) {
            .stat-num { font-size: 32px !important; }
            .stat-lbl { font-size: 9px !important; }
            .hero-name { font-size: 28px !important; }
            .hero-actions { flex-direction: column; align-items: flex-start !important; }
            .hero-actions .btn { width: 100%; justify-content: center; }
        }
    </style>

</x-app-layout>

{{-- ═══ CHARITY EMERGENCY STATUS MODAL ═══ --}}
@if($user->role === 'charity' && $user->verification_status === 'approved')
<div id="emergency-status-modal" style="display:none;position:fixed;inset:0;z-index:9999;align-items:center;justify-content:center;">
    <div onclick="closeEmergencyModal()" style="position:absolute;inset:0;background:rgba(22,8,0,0.55);backdrop-filter:blur(4px);"></div>
    <div id="emergency-modal-box" style="position:relative;background:var(--white);border-radius:24px;max-width:520px;width:90%;box-shadow:0 32px 80px rgba(22,8,0,0.25);overflow:hidden;transform:translateY(20px);opacity:0;transition:all 0.3s cubic-bezier(0.34,1.56,0.64,1);">
        <div style="background:linear-gradient(135deg,var(--red),#8B2500);padding:28px 32px 24px;position:relative;overflow:hidden;">
            <div style="position:absolute;top:-40px;right:-40px;width:120px;height:120px;border-radius:50%;background:rgba(255,255,255,0.1);"></div>
            <div style="position:relative;">
                <div style="width:48px;height:48px;background:rgba(255,255,255,0.2);border-radius:13px;display:flex;align-items:center;justify-content:center;margin-bottom:14px;"><svg width="22" height="22" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg></div>
                <h3 style="font-family:'Cormorant Garamond',serif;font-size:24px;font-weight:700;color:white;margin-bottom:4px;">Declare Area Affected</h3>
                <p style="font-size:13px;color:rgba(255,255,255,0.6);">Update your area's emergency status to help prioritize donations</p>
            </div>
        </div>
        <div style="padding:28px 32px;">
            @if(session('success'))
                <div style="background:#E4F5EB;border-radius:10px;padding:14px 20px;margin-bottom:20px;font-size:13px;font-weight:600;color:#1A7A40;display:flex;align-items:center;gap:10px;"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>{{ session('success') }}</div>
            @endif
            <form method="POST" action="{{ route('profile.emergency-status') }}">
                @csrf @method('PATCH')
                <div style="background:rgba(196,144,16,0.06);border-radius:12px;padding:16px;margin-bottom:20px;">
                    <div style="font-size:11px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:var(--muted);margin-bottom:10px;">Current Status</div>
                    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;text-align:center;">
                        <div><div style="font-size:24px;font-weight:700;color:var(--red);">{{ $user->area_severity ?? 1 }}</div><div style="font-size:10px;color:var(--muted);">Severity</div></div>
                        <div><div style="font-size:24px;font-weight:700;color:var(--dark);">{{ number_format($user->population_count ?? 0) }}</div><div style="font-size:10px;color:var(--muted);">Population</div></div>
                        <div><div style="font-size:24px;font-weight:700;color:var(--gold);">{{ $user->accessibility ?? 100 }}%</div><div style="font-size:10px;color:var(--muted);">Accessible</div></div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label" for="area_severity">Area Severity Level</label>
                    <select id="area_severity" name="area_severity" class="form-input">
                        <option value="1" {{ ($user->area_severity ?? 1) == 1 ? 'selected' : '' }}>1 - Low (Normal operations)</option>
                        <option value="2" {{ ($user->area_severity ?? 1) == 2 ? 'selected' : '' }}>2 - Medium (Minor disruption)</option>
                        <option value="3" {{ ($user->area_severity ?? 1) == 3 ? 'selected' : '' }}>3 - High (Significant impact)</option>
                        <option value="4" {{ ($user->area_severity ?? 1) == 4 ? 'selected' : '' }}>4 - Critical (Severe emergency)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" for="population_count">Affected Population</label>
                    <input type="number" id="population_count" name="population_count" class="form-input" value="{{ $user->population_count ?? 0 }}" min="0" placeholder="e.g., 5000">
                </div>
                <div class="form-group">
                    <label class="form-label" for="accessibility">Accessibility (%)</label>
                    <input type="range" id="accessibility" name="accessibility" class="form-input" value="{{ $user->accessibility ?? 100 }}" min="0" max="100" step="5" oninput="document.getElementById('accessibility-val').textContent = this.value + '%'" style="padding:8px;">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-top:8px;">
                        <p style="font-size:12px;color:var(--muted);">How accessible is your area for food delivery?</p>
                        <span id="accessibility-val" style="font-size:14px;font-weight:700;color:var(--dark);">{{ $user->accessibility ?? 100 }}%</span>
                    </div>
                </div>
                <div style="display:flex;gap:12px;margin-top:24px;">
                    <button type="button" onclick="closeEmergencyModal()" class="btn btn-ghost" style="flex:1;padding:13px;justify-content:center;">Cancel</button>
                    <button type="submit" class="btn btn-red" style="flex:2;padding:13px;justify-content:center;"><svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg> Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
function openEmergencyModal() {
    document.getElementById('emergency-status-modal').style.display = 'flex';
    setTimeout(() => { const b = document.getElementById('emergency-modal-box'); b.style.transform = 'translateY(0)'; b.style.opacity = '1'; }, 10);
}
function closeEmergencyModal() {
    const b = document.getElementById('emergency-modal-box');
    b.style.transform = 'translateY(20px)'; b.style.opacity = '0';
    setTimeout(() => { document.getElementById('emergency-status-modal').style.display = 'none'; }, 250);
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeEmergencyModal(); });
</script>
@endif