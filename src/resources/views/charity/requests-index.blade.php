<x-app-layout>
    <style>
        .requests-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 24px; }
        @media (max-width: 480px) {
            .requests-grid { grid-template-columns: 1fr !important; }
        }
    </style>
    <x-slot name="header">Charity Requests</x-slot>

    @php $user = auth()->user(); @endphp

    @if(session('success'))
        <div style="background:#E4F5EB;border:1px solid rgba(26,122,64,0.2);border-radius:10px;padding:14px 20px;margin-bottom:24px;font-size:13px;font-weight:600;color:#1A7A40;display:flex;align-items:center;gap:10px;">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- Show MY requests for charities --}}
    @if($user->role === 'charity' && !empty($myRequests))
    <div style="margin-bottom:40px;">
        <div class="section-label">Your Requests</div>
        <h2 style="font-family:'Cormorant Garamond',serif;font-size:38px;font-weight:700;color:var(--dark);line-height:1.1;margin-bottom:12px;">My Food Requests</h2>
        <p style="font-size:15px;color:var(--muted);max-width:560px;line-height:1.8;">
            Your posted food requests. Donors can fulfill these requests directly.
        </p>
    </div>

    @if($myRequests->isEmpty())
        <div style="text-align:center;padding:60px 40px;background:var(--white);border-radius:20px;border:1px solid rgba(196,144,16,0.12);margin-bottom:48px;">
            <h3 style="font-family:'Cormorant Garamond',serif;font-size:24px;font-weight:700;color:var(--dark);margin-bottom:8px;">No requests yet</h3>
            <p style="font-size:14px;color:var(--muted);">You haven't posted any food requests yet.</p>
            <a href="{{ route('charity.request.create') }}" class="btn btn-red" style="margin-top:16px;">Post a Request</a>
        </div>
    @else
        <div class="requests-grid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:24px;margin-bottom:48px;" class="stagger">
            @foreach($myRequests as $req)
            <div class="don-card" style="border-left:4px solid var(--red);">
                <div class="don-card-header">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
                        <span class="badge {{
                            $req->status === 'fulfilled' ? 'badge-green' :
                            ($req->urgency === 'critical' ? 'badge-red' :
                            ($req->urgency === 'urgent' ? 'badge-yellow' : 'badge-muted'))
                        }}">
                            @if($req->status === 'fulfilled')
                                <svg width="10" height="10" fill="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            @elseif($req->urgency === 'critical')
                                <svg width="10" height="10" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L2 22h20L12 2zm0 4l7.5 13h-15L12 6z"/></svg>
                            @endif
                            {{ $req->status === 'fulfilled' ? 'Fulfilled' : ucfirst($req->urgency) }}
                        </span>
                        <span style="font-size:11px;color:var(--muted);">{{ $req->created_at->diffForHumans() }}</span>
                    </div>
                    <h3 style="font-family:'Cormorant Garamond',serif;font-size:22px;font-weight:700;color:var(--dark);margin-bottom:4px;">{{ $req->food_name }}</h3>
                    @if($req->quantity)
                        <div style="font-size:12px;color:var(--muted);display:flex;align-items:center;gap:6px;">
                            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                            Quantity needed: {{ $req->quantity }}
                        </div>
                    @endif
                </div>
                <div class="don-card-body">
                    @if($req->description)
                        <p style="font-size:13px;color:var(--muted);line-height:1.7;margin-bottom:16px;">{{ $req->description }}</p>
                    @endif
                    @if($req->status === 'fulfilled')
                        <div style="text-align:center;font-size:13px;color:#1A7A40;font-weight:600;padding:10px;background:#E4F5EB;border-radius:8px;">
                            ✓ This request has been fulfilled
                        </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    @endif

    {{-- Show OTHER requests for charities to browse --}}
    @if(!empty($otherRequests) && !$otherRequests->isEmpty())
    <div style="margin-bottom:40px;padding-top:32px;border-top:1px solid rgba(196,144,16,0.12);">
        <div class="section-label">Browse Other Requests</div>
        <h2 style="font-family:'Cormorant Garamond',serif;font-size:32px;font-weight:700;color:var(--dark);line-height:1.1;margin-bottom:12px;">Other Charities' Needs</h2>
        <p style="font-size:15px;color:var(--muted);max-width:560px;line-height:1.8;">
            See what other charities are requesting — you can also help fulfill these if you're able.
        </p>
    </div>
    @endif
    @endif

    {{-- Show all requests for donors/admins --}}
    @if($user->role !== 'charity' || empty($myRequests))
    <div style="margin-bottom:40px;">
        <div class="section-label">Browse Requests</div>
        <h2 style="font-family:'Cormorant Garamond',serif;font-size:38px;font-weight:700;color:var(--dark);line-height:1.1;margin-bottom:12px;">What Charities Need</h2>
        <p style="font-size:15px;color:var(--muted);max-width:560px;line-height:1.8;">
            These are food requests posted by verified charity organizations. Click "Donate" to fulfill a request directly — no extra steps needed.
        </p>
    </div>
    @endif

    @if(!empty($otherRequests) && !$otherRequests->isEmpty())
    <div class="requests-grid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:24px;" class="stagger">
        @foreach($otherRequests as $req)
        <div class="don-card">
                <div class="don-card-header">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
                        <span class="badge {{
                            $req->urgency === 'critical' ? 'badge-red' :
                            ($req->urgency === 'urgent' ? 'badge-yellow' : 'badge-muted')
                        }}">
                            @if($req->urgency === 'critical')
                                <svg width="10" height="10" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L2 22h20L12 2zm0 4l7.5 13h-15L12 6zm-1 5v4h2v-4h-2zm0 6v2h2v-2h-2z"/></svg>
                            @endif
                            {{ ucfirst($req->urgency) }}
                        </span>
                        <span style="font-size:11px;color:var(--muted);">{{ $req->created_at->diffForHumans() }}</span>
                    </div>
                    <h3 style="font-family:'Cormorant Garamond',serif;font-size:22px;font-weight:700;color:var(--dark);margin-bottom:4px;">{{ $req->food_name }}</h3>
                    @if($req->quantity)
                        <div style="font-size:12px;color:var(--muted);display:flex;align-items:center;gap:6px;">
                            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                            Quantity needed: {{ $req->quantity }}
                        </div>
                    @endif
                </div>

                <div class="don-card-body">
                    <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px;padding-bottom:14px;border-bottom:1px solid rgba(196,144,16,0.1);">
                        <div style="width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,var(--red),var(--red2));display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <span style="font-size:12px;font-weight:700;color:white;">{{ strtoupper(substr($req->charity->name, 0, 1)) }}</span>
                        </div>
                        <div>
                            <div style="font-size:13px;font-weight:600;color:var(--dark);">{{ $req->charity->name }}</div>
                            @if($req->charity->organization_name)
                                <div style="font-size:11px;color:var(--muted);">{{ $req->charity->organization_name }}</div>
                            @endif
                        </div>
                        <span class="badge badge-green" style="margin-left:auto;">
                            <svg width="9" height="9" fill="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Verified
                        </span>
                    </div>

                    @if($req->description)
                        <p style="font-size:13px;color:var(--muted);line-height:1.7;margin-bottom:16px;">{{ $req->description }}</p>
                    @endif
                </div>
            </div>
        @endforeach
        </div>
    @elseif(!empty($requests))
        <div class="requests-grid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:24px;" class="stagger">
            @foreach($requests as $req)
            <div class="don-card">
                <div class="don-card-header">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
                        <span class="badge {{
                            $req->urgency === 'critical' ? 'badge-red' :
                            ($req->urgency === 'urgent' ? 'badge-yellow' : 'badge-muted')
                        }}">
                            @if($req->urgency === 'critical')
                                <svg width="10" height="10" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L2 22h20L12 2zm0 4l7.5 13h-15L12 6zm-1 5v4h2v-4h-2zm0 6v2h2v-2h-2z"/></svg>
                            @endif
                            {{ ucfirst($req->urgency) }}
                        </span>
                        <span style="font-size:11px;color:var(--muted);">{{ $req->created_at->diffForHumans() }}</span>
                    </div>
                    <h3 style="font-family:'Cormorant Garamond',serif;font-size:22px;font-weight:700;color:var(--dark);margin-bottom:4px;">{{ $req->food_name }}</h3>
                    @if($req->quantity)
                        <div style="font-size:12px;color:var(--muted);display:flex;align-items:center;gap:6px;">
                            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                            Quantity needed: {{ $req->quantity }}
                        </div>
                    @endif
                </div>

                <div class="don-card-body">
                    <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px;padding-bottom:14px;border-bottom:1px solid rgba(196,144,16,0.1);">
                        <div style="width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,var(--red),var(--red2));display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <span style="font-size:12px;font-weight:700;color:white;">{{ strtoupper(substr($req->charity->name, 0, 1)) }}</span>
                        </div>
                        <div>
                            <div style="font-size:13px;font-weight:600;color:var(--dark);">{{ $req->charity->name }}</div>
                            @if($req->charity->organization_name)
                                <div style="font-size:11px;color:var(--muted);">{{ $req->charity->organization_name }}</div>
                            @endif
                        </div>
                        <span class="badge badge-green" style="margin-left:auto;">
                            <svg width="9" height="9" fill="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Verified
                        </span>
                    </div>

                    @if($req->description)
                        <p style="font-size:13px;color:var(--muted);line-height:1.7;margin-bottom:16px;">{{ $req->description }}</p>
                    @endif

                    @if($user->role === 'donor')
                        <form id="fulfill-form-{{ $req->id }}" method="POST" action="{{ route('charity.requests.fulfill', $req->id) }}" style="display:none;">
                            @csrf
                        </form>
                        <button type="button" class="btn btn-red" style="width:100%;justify-content:center;"
                            onclick="openDonateModal({{ $req->id }}, '{{ addslashes($req->food_name) }}', '{{ addslashes($req->charity->name) }}', '{{ addslashes($req->charity->organization_name ?? '') }}', '{{ $req->urgency }}', '{{ addslashes($req->quantity ?? '') }}')">
                            Donate to this Charity
                            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                        </button>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    @endif

    {{-- ═══ DONATE CONFIRMATION MODAL ═══ --}}
    <div id="donate-modal" style="display:none;position:fixed;inset:0;z-index:9999;align-items:center;justify-content:center;">
        <div onclick="closeDonateModal()" style="position:absolute;inset:0;background:rgba(22,8,0,0.55);backdrop-filter:blur(4px);"></div>
        <div style="position:relative;background:var(--white);border-radius:24px;max-width:440px;width:90%;box-shadow:0 32px 80px rgba(22,8,0,0.25);overflow:hidden;transform:translateY(20px);opacity:0;transition:all 0.3s cubic-bezier(0.34,1.56,0.64,1);" id="modal-box">

            {{-- Header --}}
            <div style="background:linear-gradient(135deg,var(--dark),#2A0E04);padding:32px 32px 28px;position:relative;overflow:hidden;">
                <div style="position:absolute;top:-40px;right:-40px;width:140px;height:140px;border-radius:50%;background:rgba(196,144,16,0.1);"></div>
                <div style="position:absolute;top:0;left:0;bottom:0;width:3px;background:linear-gradient(180deg,var(--red),var(--gold));"></div>
                <div style="position:relative;">
                    <div style="width:52px;height:52px;background:linear-gradient(135deg,var(--red),var(--red2));border-radius:14px;display:flex;align-items:center;justify-content:center;margin-bottom:16px;box-shadow:0 6px 20px rgba(176,40,24,0.4);">
                        <svg width="24" height="24" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24"><path d="M18 8h1a4 4 0 010 8h-1"/><path d="M2 8h16v9a4 4 0 01-4 4H6a4 4 0 01-4-4V8z"/><line x1="6" y1="1" x2="6" y2="4"/><line x1="10" y1="1" x2="10" y2="4"/><line x1="14" y1="1" x2="14" y2="4"/></svg>
                    </div>
                    <h3 style="font-family:'Cormorant Garamond',serif;font-size:26px;font-weight:700;color:white;margin-bottom:4px;line-height:1.1;">Confirm Donation</h3>
                    <p style="font-size:13px;color:rgba(255,255,255,0.45);">You're about to fulfill this food request</p>
                </div>
            </div>

            {{-- Body --}}
            <div style="padding:28px 32px;">
                <div style="background:var(--lighter);border:1px solid rgba(196,144,16,0.15);border-radius:14px;padding:18px 20px;margin-bottom:16px;">
                    <div style="font-size:10px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:var(--muted);margin-bottom:6px;">Food Requested</div>
                    <div style="font-family:'Cormorant Garamond',serif;font-size:22px;font-weight:700;color:var(--dark);" id="modal-food-name">—</div>
                    <div id="modal-quantity-wrap" style="font-size:12px;color:var(--muted);margin-top:4px;display:none;">
                        Quantity: <span id="modal-quantity" style="font-weight:600;color:var(--text);"></span>
                    </div>
                </div>

                <div style="display:flex;align-items:center;gap:12px;padding:16px 20px;background:var(--lighter);border:1px solid rgba(196,144,16,0.15);border-radius:14px;margin-bottom:16px;">
                    <div style="width:40px;height:40px;border-radius:50%;background:linear-gradient(135deg,var(--red),var(--red2));display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <span id="modal-charity-initial" style="font-size:14px;font-weight:700;color:white;">—</span>
                    </div>
                    <div>
                        <div style="font-size:10px;font-weight:700;letter-spacing:1.2px;text-transform:uppercase;color:var(--muted);margin-bottom:3px;">Donating to</div>
                        <div style="font-size:14px;font-weight:700;color:var(--dark);" id="modal-charity-name">—</div>
                        <div style="font-size:11px;color:var(--muted);" id="modal-org-name"></div>
                    </div>
                    <span class="badge badge-green" style="margin-left:auto;">
                        <svg width="9" height="9" fill="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Verified
                    </span>
                </div>

                <div id="modal-urgency-notice" style="display:none;padding:12px 16px;border-radius:10px;margin-bottom:16px;font-size:13px;font-weight:600;"></div>

                <p style="font-size:13px;color:var(--muted);line-height:1.7;margin-bottom:24px;">
                    By confirming, this donation will be immediately assigned to the charity and marked as fulfilled in your history.
                </p>

                <div style="display:flex;gap:12px;">
                    <button onclick="closeDonateModal()" class="btn btn-ghost" style="flex:1;padding:14px;justify-content:center;">Cancel</button>
                    <button onclick="submitDonation()" class="btn btn-red" style="flex:2;padding:14px;justify-content:center;" id="modal-confirm-btn">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 8h1a4 4 0 010 8h-1"/><path d="M2 8h16v9a4 4 0 01-4 4H6a4 4 0 01-4-4V8z"/><line x1="6" y1="1" x2="6" y2="4"/><line x1="10" y1="1" x2="10" y2="4"/><line x1="14" y1="1" x2="14" y2="4"/></svg>
                        Yes, Donate Now
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let activeFormId = null;

        function openDonateModal(id, foodName, charityName, orgName, urgency, quantity) {
            activeFormId = id;
            document.getElementById('modal-food-name').textContent = foodName;
            document.getElementById('modal-charity-name').textContent = charityName;
            document.getElementById('modal-charity-initial').textContent = charityName.charAt(0).toUpperCase();
            document.getElementById('modal-org-name').textContent = orgName || '';

            const qtyWrap = document.getElementById('modal-quantity-wrap');
            if (quantity) {
                document.getElementById('modal-quantity').textContent = quantity;
                qtyWrap.style.display = 'block';
            } else {
                qtyWrap.style.display = 'none';
            }

            const notice = document.getElementById('modal-urgency-notice');
            if (urgency === 'critical') {
                notice.style.display = 'block';
                notice.style.background = 'rgba(176,40,24,0.08)';
                notice.style.border = '1px solid rgba(176,40,24,0.2)';
                notice.style.color = 'var(--red)';
                notice.textContent = 'This is a CRITICAL request — your donation is urgently needed.';
            } else if (urgency === 'urgent') {
                notice.style.display = 'block';
                notice.style.background = 'rgba(220,168,32,0.08)';
                notice.style.border = '1px solid rgba(220,168,32,0.2)';
                notice.style.color = '#8A6010';
                notice.textContent = 'This request is marked as urgent.';
            } else {
                notice.style.display = 'none';
            }

            const modal = document.getElementById('donate-modal');
            modal.style.display = 'flex';
            setTimeout(() => {
                document.getElementById('modal-box').style.transform = 'translateY(0)';
                document.getElementById('modal-box').style.opacity = '1';
            }, 10);
        }

        function closeDonateModal() {
            const box = document.getElementById('modal-box');
            box.style.transform = 'translateY(20px)';
            box.style.opacity = '0';
            setTimeout(() => {
                document.getElementById('donate-modal').style.display = 'none';
                activeFormId = null;
            }, 250);
        }

        function submitDonation() {
            if (!activeFormId) return;
            const btn = document.getElementById('modal-confirm-btn');
            btn.disabled = true;
            btn.innerHTML = 'Sending...';
            document.getElementById('fulfill-form-' + activeFormId).submit();
        }

        document.addEventListener('keydown', e => { if (e.key === 'Escape') closeDonateModal(); });
    </script>

    <style>
        @keyframes spin { from { transform:rotate(0deg); } to { transform:rotate(360deg); } }
    </style>

</x-app-layout>