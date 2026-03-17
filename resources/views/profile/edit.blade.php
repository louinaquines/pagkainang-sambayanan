<x-app-layout>
    <style>
        .profile-grid { display: grid; grid-template-columns: 280px 1fr; gap: 32px; align-items: start; }
        .profile-form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        @media (max-width: 1024px) {
            .profile-grid { grid-template-columns: 1fr !important; }
        }
        @media (max-width: 640px) {
            .profile-form-grid { grid-template-columns: 1fr !important; }
        }
    </style>
    <x-slot name="header">Profile</x-slot>

    <div class="profile-grid" style="display:grid;grid-template-columns:280px 1fr;gap:32px;align-items:start;">

        {{-- SIDEBAR --}}
        <div style="display:flex;flex-direction:column;gap:16px;position:sticky;top:100px;">
            {{-- Avatar card --}}
            <div class="card" style="padding:28px;text-align:center;">
                <div style="width:72px;height:72px;background:linear-gradient(135deg,var(--red),var(--red2));border-radius:20px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-family:'Cormorant Garamond',serif;font-size:32px;font-weight:700;color:white;box-shadow:0 8px 24px rgba(176,40,24,0.3);">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div style="font-family:'Cormorant Garamond',serif;font-size:20px;font-weight:700;color:var(--dark);">{{ auth()->user()->name }}</div>
                <div style="font-size:12px;color:var(--muted);margin-top:4px;text-transform:capitalize;letter-spacing:0.5px;">{{ auth()->user()->role }}</div>
                @if(auth()->user()->role === 'charity')
                <div style="margin-top:12px;">
                    <span class="badge {{ auth()->user()->verification_status === 'approved' ? 'badge-green' : 'badge-yellow' }}">
                        <span class="badge-dot"></span>
                        {{ ucfirst(auth()->user()->verification_status) }}
                    </span>
                </div>
                @endif
            </div>

            {{-- Nav links --}}
            <div class="card" style="padding:8px;overflow:hidden;">
                <a href="#profile-info" style="display:flex;align-items:center;gap:12px;padding:12px 16px;border-radius:10px;text-decoration:none;color:var(--red);background:rgba(176,40,24,0.06);font-size:13px;font-weight:600;">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    Profile Information
                </a>
                <a href="#update-password" style="display:flex;align-items:center;gap:12px;padding:12px 16px;border-radius:10px;text-decoration:none;color:var(--brown);font-size:13px;font-weight:500;margin-top:2px;transition:background 0.15s;"
                   onmouseover="this.style.background='rgba(196,144,16,0.07)'" onmouseout="this.style.background='transparent'">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                    Update Password
                </a>
                <a href="#delete-account" style="display:flex;align-items:center;gap:12px;padding:12px 16px;border-radius:10px;text-decoration:none;color:var(--red);font-size:13px;font-weight:500;margin-top:2px;transition:background 0.15s;"
                   onmouseover="this.style.background='rgba(176,40,24,0.06)'" onmouseout="this.style.background='transparent'">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a1 1 0 011-1h4a1 1 0 011 1v2"/></svg>
                    Delete Account
                </a>
            </div>
        </div>

        {{-- MAIN CONTENT --}}
        <div style="display:flex;flex-direction:column;gap:24px;">

            {{-- Profile Information --}}
            <div class="card" style="padding:36px;" id="profile-info">
                <div style="margin-bottom:28px;padding-bottom:20px;border-bottom:1px solid rgba(196,144,16,0.1);">
                    <h3 style="font-family:'Cormorant Garamond',serif;font-size:24px;font-weight:700;color:var(--dark);margin-bottom:6px;">Profile Information</h3>
                    <p style="font-size:13px;color:var(--muted);">Update your account name and email address.</p>
                </div>

                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    @method('patch')

                    <div class="profile-form-grid" style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
                        <div class="form-group">
                            <label class="form-label" for="name">Full Name</label>
                            <input type="text" id="name" name="name" class="form-input"
                                value="{{ old('name', auth()->user()->name) }}" required autofocus>
                            @error('name') <p class="form-error">{{ $message }}</p> @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="email">Email Address</label>
                            <input type="email" id="email" name="email" class="form-input"
                                value="{{ old('email', auth()->user()->email) }}" required>
                            @error('email') <p class="form-error">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div style="display:flex;align-items:center;gap:16px;margin-top:8px;">
                        <button type="submit" class="btn btn-red" style="padding:12px 28px;">Save Changes</button>
                        @if(session('status') === 'profile-updated')
                            <span style="font-size:13px;color:#1A7A40;font-weight:600;display:flex;align-items:center;gap:6px;">
                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                                Saved successfully
                            </span>
                        @endif
                    </div>
                </form>
            </div>

            {{-- Update Password --}}
            <div class="card" style="padding:36px;" id="update-password">
                <div style="margin-bottom:28px;padding-bottom:20px;border-bottom:1px solid rgba(196,144,16,0.1);">
                    <h3 style="font-family:'Cormorant Garamond',serif;font-size:24px;font-weight:700;color:var(--dark);margin-bottom:6px;">Update Password</h3>
                    <p style="font-size:13px;color:var(--muted);">Ensure your account is using a long, random password to stay secure.</p>
                </div>

                <form method="POST" action="{{ route('password.update') }}">
                    @csrf
                    @method('put')

                    <div style="display:flex;flex-direction:column;gap:20px;">
                        <div class="form-group" style="margin:0;">
                            <label class="form-label" for="current_password">Current Password</label>
                            <input type="password" id="current_password" name="current_password" class="form-input" autocomplete="current-password">
                            @error('current_password') <p class="form-error">{{ $message }}</p> @enderror
                        </div>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
                            <div class="form-group" style="margin:0;">
                                <label class="form-label" for="password">New Password</label>
                                <input type="password" id="password" name="password" class="form-input" autocomplete="new-password">
                                @error('password') <p class="form-error">{{ $message }}</p> @enderror
                            </div>
                            <div class="form-group" style="margin:0;">
                                <label class="form-label" for="password_confirmation">Confirm Password</label>
                                <input type="password" id="password_confirmation" name="password_confirmation" class="form-input" autocomplete="new-password">
                            </div>
                        </div>
                    </div>

                    <div style="display:flex;align-items:center;gap:16px;margin-top:28px;">
                        <button type="submit" class="btn btn-dark" style="padding:12px 28px;">Update Password</button>
                        @if(session('status') === 'password-updated')
                            <span style="font-size:13px;color:#1A7A40;font-weight:600;display:flex;align-items:center;gap:6px;">
                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                                Password updated
                            </span>
                        @endif
                    </div>
                </form>
            </div>

            {{-- Delete Account --}}
            <div class="card" style="padding:36px;border-top:3px solid var(--red);" id="delete-account">
                <div style="margin-bottom:20px;">
                    <h3 style="font-family:'Cormorant Garamond',serif;font-size:24px;font-weight:700;color:var(--dark);margin-bottom:6px;">Delete Account</h3>
                    <p style="font-size:13px;color:var(--muted);line-height:1.7;">Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.</p>
                </div>
                <form method="POST" action="{{ route('profile.destroy') }}">
                    @csrf
                    @method('delete')
                    <div class="form-group" style="max-width:360px;">
                        <label class="form-label" for="delete_password">Confirm your password to delete</label>
                        <input type="password" id="delete_password" name="password" class="form-input" placeholder="Enter your password">
                        @error('password', 'userDeletion') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <button type="submit" class="btn btn-red" style="padding:12px 28px;"
                        onclick="return confirm('Are you sure? This action cannot be undone.')">
                        Delete Account Permanently
                    </button>
                </form>
            </div>

        </div>
    </div>

</x-app-layout>