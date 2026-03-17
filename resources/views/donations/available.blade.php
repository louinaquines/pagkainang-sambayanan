<x-app-layout>
    <x-slot name="header">Available Food Donations</x-slot>

    @php $user = auth()->user(); @endphp

    {{-- Stats bar --}}
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:36px;flex-wrap:wrap;gap:16px;">
        <div>
            <div class="section-label">Browse</div>
            <h2 style="font-family:'Cormorant Garamond',serif;font-size:32px;font-weight:700;color:var(--dark);line-height:1.1;">Available Food Donations</h2>
        </div>
        <div style="display:flex;align-items:center;gap:12px;">
            <div style="background:var(--white);border:1px solid rgba(196,144,16,0.15);border-radius:12px;padding:12px 20px;text-align:center;min-width:100px;">
                <div style="font-family:'Cormorant Garamond',serif;font-size:28px;font-weight:700;color:var(--red);line-height:1;">{{ $donations->count() }}</div>
                <div style="font-size:10px;color:var(--muted);text-transform:uppercase;letter-spacing:1.5px;margin-top:4px;font-weight:600;">Available</div>
            </div>
        </div>
    </div>

    @if($donations->isEmpty())
        <div class="card" style="padding:80px 40px;text-align:center;">
            <div style="width:72px;height:72px;background:rgba(196,144,16,0.08);border-radius:20px;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
                <svg width="32" height="32" fill="none" stroke="var(--gold)" stroke-width="1.5" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
            </div>
            <h3 style="font-family:'Cormorant Garamond',serif;font-size:24px;font-weight:700;color:var(--dark);margin-bottom:8px;">No donations available yet</h3>
            <p style="font-size:14px;color:var(--muted);margin-bottom:24px;">
                @if($user->role === 'donor') Be the first to post a food donation and make a difference.
                @else Check back soon — donors will be posting food donations shortly. @endif
            </p>
            @if($user->role === 'donor')
                <a href="{{ route('donations.create') }}" class="btn btn-red">Post a Donation</a>
            @endif
        </div>
    <style>
        .donations-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; }
        @media (max-width: 1024px) {
            .donations-grid { grid-template-columns: repeat(2, 1fr) !important; }
        }
        @media (max-width: 640px) {
            .donations-grid { grid-template-columns: 1fr !important; }
        }
    </style>
    @else
        <div class="donations-grid" style="display:grid;grid-template-columns:repeat(3,1fr);gap:24px;">
            @foreach($donations as $donation)
            @php
                $myRequest   = $user->role === 'charity' ? $donation->claims->firstWhere('charity_id', $user->id) : null;
                $claimCount  = $donation->claims->where('status','pending')->count();
            @endphp
            <div class="don-card">
                <div class="don-card-header">
                    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;position:relative;z-index:1;">
                        <div style="width:48px;height:48px;background:linear-gradient(135deg,var(--red),var(--red2));border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 4px 12px rgba(176,40,24,0.25);">
                            <svg width="22" height="22" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24"><path d="M18 8h1a4 4 0 010 8h-1"/><path d="M2 8h16v9a4 4 0 01-4 4H6a4 4 0 01-4-4V8z"/><line x1="6" y1="1" x2="6" y2="4"/><line x1="10" y1="1" x2="10" y2="4"/><line x1="14" y1="1" x2="14" y2="4"/></svg>
                        </div>
                        <span class="badge badge-green">
                            <span class="badge-dot"></span>
                            Available
                        </span>
                    </div>
                    <h3 style="font-family:'Cormorant Garamond',serif;font-size:20px;font-weight:700;color:var(--dark);margin-top:16px;line-height:1.3;position:relative;z-index:1;">{{ $donation->description }}</h3>
                </div>

                <div class="don-card-body">
                    <div style="display:flex;flex-direction:column;gap:8px;margin-bottom:20px;">
                        <div style="display:flex;align-items:center;gap:10px;font-size:13px;color:var(--muted);">
                            <svg width="14" height="14" fill="none" stroke="var(--gold)" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
                            <span>Target: <strong style="color:var(--text);">{{ $donation->target_audience }}</strong></span>
                        </div>
                        @if($donation->affected_area)
                        <div style="display:flex;align-items:center;gap:10px;font-size:13px;color:var(--red);">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <span>For: <strong style="color:var(--text);">{{ $donation->affected_area }}</strong></span>
                        </div>
                        @endif
                        <div style="display:flex;align-items:center;gap:10px;font-size:13px;color:var(--muted);">
                            <svg width="14" height="14" fill="none" stroke="var(--red)" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            <span>Donor: <strong style="color:var(--text);">{{ $donation->donor->name }}</strong></span>
                        </div>
                        <div style="display:flex;align-items:center;gap:10px;font-size:13px;color:var(--muted);">
                            <svg width="14" height="14" fill="none" stroke="var(--muted)" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            <span>{{ $donation->created_at->diffForHumans() }}</span>
                        </div>
                    </div>

                    <div style="padding-top:16px;border-top:1px solid rgba(196,144,16,0.1);">

                        {{-- ── CHARITY VIEW ── --}}
                        @if($user->role === 'charity')
                            @if($user->verification_status === 'approved')
                                @if($myRequest && $myRequest->status === 'pending')
                                    <div style="text-align:center;font-size:13px;color:#8A6010;font-weight:600;padding:10px 14px;background:rgba(196,144,16,0.08);border:1px solid rgba(196,144,16,0.2);border-radius:8px;">
                                        Request sent — awaiting donor approval
                                    </div>
                                @elseif($myRequest && $myRequest->status === 'accepted')
                                    @if(!$donation->feedback)
                                        <a href="{{ route('feedback.create', $donation->id) }}" class="btn btn-gold" style="width:100%;padding:12px;justify-content:center;">
                                            Submit Feedback
                                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                                        </a>
                                    @else
                                        <div style="text-align:center;font-size:13px;color:#1A7A40;font-weight:600;padding:10px;background:#E4F5EB;border-radius:8px;">
                                            Feedback submitted
                                        </div>
                                    @endif
                                @elseif($myRequest && $myRequest->status === 'rejected')
                                    <div style="text-align:center;font-size:13px;color:var(--red);font-weight:600;padding:10px;background:rgba(176,40,24,0.06);border-radius:8px;">
                                        Request was not accepted
                                    </div>
                                @else
                                    @if($emergencyMode)
                                        <div style="text-align:center;font-size:13px;color:var(--red);font-weight:600;padding:10px 14px;background:rgba(176,40,24,0.08);border:1px solid rgba(176,40,24,0.2);border-radius:8px;">
                                            Claims blocked during Emergency Mode
                                        </div>
                                    @else
                                        <form method="POST" action="{{ route('donations.claim', $donation->id) }}">
                                            @csrf
                                            <button type="submit" class="btn btn-red" style="width:100%;padding:12px;justify-content:center;">
                                                Request This Donation
                                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                                            </button>
                                        </form>
                                    @endif
                                @endif
                            @else
                                <div style="text-align:center;font-size:12px;color:var(--muted);padding:10px;font-style:italic;">
                                    Pending charity verification
                                </div>
                            @endif

                        {{-- ── DONOR VIEW: see who requested ── --}}
                        @elseif($user->role === 'donor' && $donation->user_id === $user->id)
                            @if($donation->claims->where('status','pending')->count() > 0)
                                <div style="margin-bottom:10px;">
                                    <div style="font-size:11px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:var(--muted);margin-bottom:8px;">
                                        {{ $claimCount }} {{ Str::plural('charity', $claimCount) }} requesting
                                    </div>
                                    @foreach($donation->claims->where('status','pending') as $claim)
                                    <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 12px;background:var(--lighter);border:1px solid rgba(196,144,16,0.12);border-radius:10px;margin-bottom:6px;">
                                        <div>
                                            <div style="font-size:13px;font-weight:600;color:var(--dark);">{{ $claim->charity->name }}</div>
                                            @if($claim->charity->organization_name)
                                                <div style="font-size:11px;color:var(--muted);">{{ $claim->charity->organization_name }}</div>
                                            @endif
                                        </div>
                                        <form method="POST" action="{{ route('donations.claims.accept', $claim->id) }}">
                                            @csrf
                                            <button type="submit" class="btn btn-red" style="padding:6px 14px;font-size:11px;">
                                                Accept
                                            </button>
                                        </form>
                                    </div>
                                    @endforeach
                                </div>
                            @else
                                <div style="text-align:center;font-size:12px;color:var(--muted);padding:10px;font-style:italic;">
                                    No requests yet
                                </div>
                            @endif
                        @endif

                        {{-- Admin: no action --}}

                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @endif

</x-app-layout>