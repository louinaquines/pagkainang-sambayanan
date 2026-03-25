<nav class="main-nav">
    <div class="nav-inner">

        {{-- Logo --}}
        <a href="{{ route('dashboard') }}" class="nav-logo">
            <img src="{{ asset('logo.jpg') }}" alt="Logo" style="width:36px;height:36px;border-radius:10px;object-fit:cover;">
            <div>
                <div class="nav-logo-main">Pagkainang</div>
                <div class="nav-logo-sub">Sambayanan</div>
            </div>
        </a>

        {{-- Nav Links --}}
        <div class="nav-links" id="nav-links">

            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a>

            @if(auth()->user()->role === 'donor')
                <a href="{{ route('donations.create') }}"        class="nav-link {{ request()->routeIs('donations.create')        ? 'active' : '' }}">Post Donation</a>
                <a href="{{ route('charity.requests.index') }}"  class="nav-link {{ request()->routeIs('charity.requests.index')  ? 'active' : '' }}">Charity Requests</a>
                <a href="{{ route('donations.available') }}"     class="nav-link {{ request()->routeIs('donations.available')     ? 'active' : '' }}">Available Food</a>
                <a href="{{ route('feedback.index') }}"          class="nav-link {{ request()->routeIs('feedback.index')          ? 'active' : '' }}">Feedback Log</a>
                <a href="{{ route('donations.index') }}"         class="nav-link {{ request()->routeIs('donations.index')         ? 'active' : '' }}">History</a>
                @php $donorUnread = \App\Models\Notification::where('user_id', auth()->id())->where('is_read', false)->count(); @endphp
                <a href="{{ route('notifications.index') }}" class="nav-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}" style="position:relative;display:inline-flex;align-items:center;gap:6px;">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 01-3.46 0"/></svg>
                    @if($donorUnread > 0)<span style="position:absolute;top:-4px;right:-8px;width:16px;height:16px;background:var(--red);border-radius:50%;font-size:9px;font-weight:700;color:white;display:flex;align-items:center;justify-content:center;">{{ $donorUnread }}</span>@endif
                </a>

            @elseif(auth()->user()->role === 'charity')
                @if(!auth()->user()->organization_name || auth()->user()->verification_status === 'unsubmitted')
                    <a href="{{ route('charity.register') }}" class="nav-link nav-admin {{ request()->routeIs('charity.register') ? 'active' : '' }}">Register Organization</a>
                @elseif(auth()->user()->verification_status === 'pending')
                    <span class="nav-link" style="opacity:0.55;cursor:default;">Verification Pending</span>
                @elseif(auth()->user()->verification_status === 'rejected')
                    <a href="{{ route('charity.register') }}" class="nav-link nav-admin">Re-apply</a>
                @elseif(auth()->user()->verification_status === 'approved')
                    <a href="{{ route('charity.request.create') }}" class="nav-link {{ request()->routeIs('charity.request.create') ? 'active' : '' }}">Post a Request</a>
                    <a href="{{ route('donations.available') }}"    class="nav-link {{ request()->routeIs('donations.available')    ? 'active' : '' }}">Available Food</a>
                    <a href="{{ route('feedback.index') }}"         class="nav-link {{ request()->routeIs('feedback.index')         ? 'active' : '' }}">Feedback Log</a>
                    <a href="{{ route('donations.index') }}"        class="nav-link {{ request()->routeIs('donations.index')        ? 'active' : '' }}">History</a>
                    @php $unread = \App\Models\Notification::where('user_id', auth()->id())->where('is_read', false)->count(); @endphp
                    <a href="{{ route('notifications.index') }}" class="nav-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}" style="position:relative;display:inline-flex;align-items:center;gap:6px;">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 01-3.46 0"/></svg>
                        @if($unread > 0)<span style="position:absolute;top:-4px;right:-8px;width:16px;height:16px;background:var(--red);border-radius:50%;font-size:9px;font-weight:700;color:white;display:flex;align-items:center;justify-content:center;">{{ $unread }}</span>@endif
                    </a>
                @endif

            @elseif(auth()->user()->role === 'admin')
                <a href="{{ route('donations.available') }}" class="nav-link {{ request()->routeIs('donations.available') ? 'active' : '' }}">Available Food</a>
                <a href="{{ route('feedback.index') }}"      class="nav-link {{ request()->routeIs('feedback.index')      ? 'active' : '' }}">Feedback Log</a>
                <a href="{{ route('donations.index') }}"     class="nav-link {{ request()->routeIs('donations.index')     ? 'active' : '' }}">History</a>

                @php $adminPending = \App\Models\User::where('role','charity')->where('verification_status','pending')->whereNotNull('organization_name')->where('organization_name','!=','')->count(); @endphp

                {{-- Admin dropdown (vanilla JS) --}}
                <div class="nav-admin-wrap" style="position:relative;">
                    <button type="button" id="admin-dropdown-btn"
                            class="nav-link nav-admin {{ request()->routeIs('admin.*') ? 'active' : '' }}"
                            style="display:inline-flex;align-items:center;gap:6px;background:none;border:none;cursor:pointer;padding:8px 14px;font:inherit;">
                        Admin Panel
                        <svg id="admin-chevron" style="transition:transform 0.2s;" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                        </svg>
                        @if($adminPending > 0)
                            <span style="width:16px;height:16px;background:var(--red);border-radius:50%;font-size:9px;font-weight:700;color:white;display:inline-flex;align-items:center;justify-content:center;margin-left:2px;">{{ $adminPending }}</span>
                        @endif
                    </button>

                    <div id="admin-dropdown"
                         style="display:none;position:absolute;top:calc(100% + 12px);left:50%;transform:translateX(-50%);min-width:220px;background:var(--white);border:1px solid rgba(196,144,16,0.15);border-radius:14px;box-shadow:0 16px 48px rgba(22,8,0,0.14);overflow:hidden;z-index:500;">
                        <div style="padding:12px 16px;border-bottom:1px solid rgba(196,144,16,0.1);background:var(--lighter);">
                            <div style="font-size:10px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:var(--muted);">Admin Panel</div>
                        </div>
                        <div style="padding:6px;">
                            <a href="{{ route('admin.charities') }}" style="display:flex;align-items:center;gap:12px;padding:11px 14px;border-radius:9px;text-decoration:none;color:var(--dark);font-size:13px;font-weight:600;transition:background 0.15s;"
                               onmouseover="this.style.background='rgba(196,144,16,0.08)'" onmouseout="this.style.background=''">
                                <div style="width:30px;height:30px;border-radius:8px;background:rgba(196,144,16,0.1);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <svg width="14" height="14" fill="none" stroke="var(--gold)" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
                                </div>
                                <div>
                                    <div>Manage Charities</div>
                                    @if($adminPending > 0)<div style="font-size:10px;color:var(--red);font-weight:700;">{{ $adminPending }} pending</div>@endif
                                </div>
                            </a>
                            <a href="{{ route('admin.dashboard') }}" style="display:flex;align-items:center;gap:12px;padding:11px 14px;border-radius:9px;text-decoration:none;color:var(--dark);font-size:13px;font-weight:600;transition:background 0.15s;"
                               onmouseover="this.style.background='rgba(176,40,24,0.06)'" onmouseout="this.style.background=''">
                                <div style="width:30px;height:30px;border-radius:8px;background:rgba(176,40,24,0.08);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <svg width="14" height="14" fill="none" stroke="var(--red)" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                </div>
                                <div>
                                    <div>Priority Dashboard</div>
                                    <div style="font-size:10px;color:var(--muted);font-weight:400;">Disaster response scores</div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            @endif

        </div>

        {{-- Mobile toggle --}}
        <button class="nav-menu-toggle" id="nav-toggle" type="button" aria-label="Toggle menu">
            <span></span>
            <span></span>
            <span></span>
        </button>

        {{-- User Menu --}}
        <div class="nav-user-wrap" style="position:relative;">
            <button class="nav-user-btn" id="user-menu-btn" type="button">
                <div class="nav-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                <span class="nav-user-name">{{ auth()->user()->name }}</span>
                <svg id="user-chevron" style="transition:transform 0.25s;color:#7A6050;" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            <div id="user-dropdown" class="nav-dropdown" style="display:none;">
                <div class="nav-dropdown-head">
                    <div class="nav-dd-name">{{ auth()->user()->name }}</div>
                    <div class="nav-dd-role">{{ ucfirst(auth()->user()->role) }}</div>
                    <div class="nav-dd-email">{{ auth()->user()->email }}</div>
                </div>
                <a href="{{ route('profile.edit') }}" class="nav-dd-item">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    Profile Settings
                </a>
                <a href="{{ route('logout.get') }}" class="nav-dd-item dd-logout">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                    Sign Out
                </a>
            </div>
        </div>

    </div>
</nav>

<script>
(function () {
    // ── Mobile nav toggle ──────────────────────────────────────
    var navToggle = document.getElementById('nav-toggle');
    var navLinks  = document.getElementById('nav-links');

    if (navToggle && navLinks) {
        navToggle.addEventListener('click', function (e) {
            e.stopPropagation();
            var open = navLinks.classList.toggle('nav-open');
            navToggle.classList.toggle('is-open', open);
        });

        // Close when a nav link is clicked
        navLinks.querySelectorAll('a').forEach(function (a) {
            a.addEventListener('click', function () {
                navLinks.classList.remove('nav-open');
                navToggle.classList.remove('is-open');
            });
        });

        // Close on outside click
        document.addEventListener('click', function (e) {
            if (!navToggle.contains(e.target) && !navLinks.contains(e.target)) {
                navLinks.classList.remove('nav-open');
                navToggle.classList.remove('is-open');
            }
        });
    }

    // ── User dropdown ──────────────────────────────────────────
    var userBtn = document.getElementById('user-menu-btn');
    var userDd  = document.getElementById('user-dropdown');
    var userChv = document.getElementById('user-chevron');

    if (userBtn && userDd) {
        userBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            var open = userDd.style.display === 'none' || userDd.style.display === '';
            userDd.style.display = open ? 'block' : 'none';
            if (userChv) userChv.style.transform = open ? 'rotate(180deg)' : '';
        });

        document.addEventListener('click', function (e) {
            if (!userBtn.contains(e.target)) {
                userDd.style.display = 'none';
                if (userChv) userChv.style.transform = '';
            }
        });
    }

    // ── Admin dropdown ─────────────────────────────────────────
    var adminBtn = document.getElementById('admin-dropdown-btn');
    var adminDd  = document.getElementById('admin-dropdown');
    var adminChv = document.getElementById('admin-chevron');

    if (adminBtn && adminDd) {
        adminBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            var open = adminDd.style.display === 'none' || adminDd.style.display === '';
            adminDd.style.display = open ? 'block' : 'none';
            if (adminChv) adminChv.style.transform = open ? 'rotate(180deg)' : '';
        });

        document.addEventListener('click', function (e) {
            if (!adminBtn.contains(e.target)) {
                adminDd.style.display = 'none';
                if (adminChv) adminChv.style.transform = '';
            }
        });
    }
})();
</script>