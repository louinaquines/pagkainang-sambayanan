<x-app-layout>
    <x-slot name="header">Charity Verification</x-slot>
    
    <style>
        .charity-stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; }
        @media (max-width: 768px) {
            .charity-stats { grid-template-columns: 1fr !important; }
            .charity-card-grid { grid-template-columns: 1fr !important; }
        }
        @media (max-width: 480px) {
            .charity-stats > div { padding: 14px !important; }
            .charity-stats > div > div:first-child { font-size: 24px !important; }
        }
    </style>

    @if(session('success'))
        <div style="background:#E4F5EB;border:1px solid rgba(26,122,64,0.2);border-radius:10px;padding:14px 20px;margin-bottom:24px;font-size:13px;font-weight:600;color:#1A7A40;">
            {{ session('success') }}
        </div>
    @endif

    <div style="margin-bottom:40px;">
        <div class="section-label">Admin</div>
        <h2 style="font-family:'Cormorant Garamond',serif;font-size:38px;font-weight:700;color:var(--dark);line-height:1.1;margin-bottom:12px;">Manage Charities</h2>
        <p style="font-size:15px;color:var(--muted);max-width:560px;line-height:1.8;">Review legitimacy documents and approve or reject charity organizations applying on the platform.</p>
    </div>

    {{-- Stats --}}
    @php
        $pending  = $charities->where('verification_status','pending')->count();
        $approved = $charities->where('verification_status','approved')->count();
        $rejected = $charities->where('verification_status','rejected')->count();
    @endphp
    <div class="charity-stats" style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:36px;">
        <div style="background:var(--white);border:1px solid rgba(196,144,16,0.15);border-radius:14px;padding:20px 24px;display:flex;align-items:center;gap:16px;">
            <div style="width:44px;height:44px;background:rgba(220,168,32,0.12);border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="20" height="20" fill="none" stroke="#DCA820" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
            <div>
                <div style="font-family:'Cormorant Garamond',serif;font-size:30px;font-weight:700;color:var(--dark);line-height:1;">{{ $pending }}</div>
                <div style="font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:1.2px;font-weight:600;">Pending</div>
            </div>
        </div>
        <div style="background:var(--white);border:1px solid rgba(196,144,16,0.15);border-radius:14px;padding:20px 24px;display:flex;align-items:center;gap:16px;">
            <div style="width:44px;height:44px;background:rgba(26,122,64,0.1);border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="20" height="20" fill="none" stroke="#1A7A40" stroke-width="2" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
            </div>
            <div>
                <div style="font-family:'Cormorant Garamond',serif;font-size:30px;font-weight:700;color:var(--dark);line-height:1;">{{ $approved }}</div>
                <div style="font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:1.2px;font-weight:600;">Approved</div>
            </div>
        </div>
        <div style="background:var(--white);border:1px solid rgba(196,144,16,0.15);border-radius:14px;padding:20px 24px;display:flex;align-items:center;gap:16px;">
            <div style="width:44px;height:44px;background:rgba(176,40,24,0.1);border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="20" height="20" fill="none" stroke="var(--red)" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </div>
            <div>
                <div style="font-family:'Cormorant Garamond',serif;font-size:30px;font-weight:700;color:var(--dark);line-height:1;">{{ $rejected }}</div>
                <div style="font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:1.2px;font-weight:600;">Rejected</div>
            </div>
        </div>
    </div>

    @if($charities->isEmpty())
        <div class="card" style="padding:80px 40px;text-align:center;">
            <div style="width:64px;height:64px;background:rgba(196,144,16,0.08);border-radius:16px;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
                <svg width="28" height="28" fill="none" stroke="var(--gold)" stroke-width="1.5" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            </div>
            <h3 style="font-family:'Cormorant Garamond',serif;font-size:24px;font-weight:700;color:var(--dark);margin-bottom:8px;">No charity registrations yet</h3>
            <p style="font-size:14px;color:var(--muted);">Charities will appear here once they register on the platform.</p>
        </div>
    @else
        <div style="display:flex;flex-direction:column;gap:20px;">
            @foreach($charities as $charity)
            <div class="card" style="padding:32px;border-left:4px solid {{
                $charity->verification_status === 'approved' ? '#1A7A40' :
                ($charity->verification_status === 'rejected' ? 'var(--red)' : '#DCA820')
            }};">
                <div style="display:grid;grid-template-columns:1fr auto;gap:24px;align-items:start;">

                    {{-- Left: org info --}}
                    <div>
                        <div style="display:flex;align-items:center;gap:12px;margin-bottom:14px;flex-wrap:wrap;">
                            <h3 style="font-family:'Cormorant Garamond',serif;font-size:22px;font-weight:700;color:var(--dark);margin:0;">{{ $charity->organization_name }}</h3>
                            <span class="badge {{
                                $charity->verification_status === 'approved' ? 'badge-green' :
                                ($charity->verification_status === 'rejected'  ? 'badge-red'   : 'badge-yellow')
                            }}">
                                <span class="badge-dot"></span>
                                {{ ucfirst($charity->verification_status) }}
                            </span>
                        </div>

                        @if($charity->organization_description)
                            <p style="font-size:13px;color:var(--muted);line-height:1.7;margin-bottom:16px;max-width:600px;">{{ $charity->organization_description }}</p>
                        @endif

                        <div style="display:flex;flex-wrap:wrap;gap:20px;font-size:13px;color:var(--muted);">
                            <div style="display:flex;align-items:center;gap:7px;">
                                <svg width="13" height="13" fill="none" stroke="var(--gold)" stroke-width="2" viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                                <span>{{ $charity->email }}</span>
                            </div>
                            @if($charity->contact_number)
                            <div style="display:flex;align-items:center;gap:7px;">
                                <svg width="13" height="13" fill="none" stroke="var(--gold)" stroke-width="2" viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 9.81 19.79 19.79 0 01.1 1.18 2 2 0 012.08 0h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.09 7.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"/></svg>
                                <span>{{ $charity->contact_number }}</span>
                            </div>
                            @endif
                            @if($charity->address)
                            <div style="display:flex;align-items:center;gap:7px;">
                                <svg width="13" height="13" fill="none" stroke="var(--gold)" stroke-width="2" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                <span>{{ $charity->address }}</span>
                            </div>
                            @endif
                            <div style="display:flex;align-items:center;gap:7px;">
                                <svg width="13" height="13" fill="none" stroke="var(--gold)" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                <span>Applied {{ $charity->created_at->diffForHumans() }}</span>
                            </div>
                        </div>

                        {{-- Legitimacy document --}}
                        <div style="margin-top:20px;padding:16px 20px;background:var(--lighter);border:1px solid rgba(196,144,16,0.15);border-radius:12px;display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;">
                            <div style="display:flex;align-items:center;gap:12px;">
                                <div style="width:38px;height:38px;background:rgba(196,144,16,0.1);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <svg width="18" height="18" fill="none" stroke="var(--gold)" stroke-width="1.8" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                </div>
                                <div>
                                    <div style="font-size:12px;font-weight:700;color:var(--dark);margin-bottom:2px;">Legitimacy Document</div>
                                    @if($charity->legitimacy_document)
                                        <div style="font-size:11px;color:var(--muted);">{{ basename($charity->legitimacy_document) }}</div>
                                    @else
                                        <div style="font-size:11px;color:var(--muted);font-style:italic;">No document uploaded</div>
                                    @endif
                                </div>
                            </div>
                            @if($charity->legitimacy_document)
                                <a href="{{ route('admin.serve', $charity->legitimacy_document) }}"
                                   target="_blank"
                                   class="btn btn-gold"
                                   style="padding:9px 20px;font-size:12px;">
                                    View Document
                                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                                </a>
                            @endif
                        </div>
                    </div>

                    {{-- Right: action buttons --}}
                    <div style="display:flex;flex-direction:column;gap:10px;min-width:240px;">
                        @if($charity->verification_status !== 'approved')
                        <form method="POST" action="{{ route('admin.charities.approve', $charity->id) }}">
                            @csrf
                            <div style="margin-bottom:10px;">
                                <label style="font-size:10px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:var(--muted);display:block;margin-bottom:4px;">Area Severity (1-4)</label>
                                <select name="area_severity" class="form-input" style="padding:8px 12px;font-size:12px;">
                                    <option value="1" {{ ($charity->area_severity ?? 1) == 1 ? 'selected' : '' }}>1 - Low</option>
                                    <option value="2" {{ ($charity->area_severity ?? 1) == 2 ? 'selected' : '' }}>2 - Medium</option>
                                    <option value="3" {{ ($charity->area_severity ?? 1) == 3 ? 'selected' : '' }}>3 - High</option>
                                    <option value="4" {{ ($charity->area_severity ?? 1) == 4 ? 'selected' : '' }}>4 - Critical</option>
                                </select>
                            </div>
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:10px;">
                                <div>
                                    <label style="font-size:10px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:var(--muted);display:block;margin-bottom:4px;">Population</label>
                                    <input type="number" name="population_count" class="form-input" style="padding:8px 12px;font-size:12px;" value="{{ $charity->population_count ?? 0 }}" placeholder="0">
                                </div>
                                <div>
                                    <label style="font-size:10px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:var(--muted);display:block;margin-bottom:4px;">Access %</label>
                                    <input type="number" name="accessibility" class="form-input" style="padding:8px 12px;font-size:12px;" value="{{ $charity->accessibility ?? 100 }}" min="0" max="100" placeholder="100">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-gold" style="width:100%;padding:10px 18px;font-size:13px;">
                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                                Approve
                            </button>
                        </form>
                        @endif
                        @if($charity->verification_status !== 'rejected')
                        <form method="POST" action="{{ route('admin.charities.reject', $charity->id) }}">
                            @csrf
                            <button type="submit" class="btn btn-red" style="width:100%;padding:10px 18px;font-size:13px;">
                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                Reject
                            </button>
                        </form>
                        @endif
                        @if($charity->verification_status === 'approved')
                        <div style="text-align:center;font-size:12px;color:#1A7A40;font-weight:600;padding:10px;background:#E4F5EB;border-radius:8px;">
                            Verified
                        </div>
                        @endif
                    </div>

                </div>
            </div>
            @endforeach
        </div>
    @endif

</x-app-layout>