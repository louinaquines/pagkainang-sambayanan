<x-guest-layout>
    <style>
        @media (max-width: 640px) {
            .auth-card { padding: 24px 20px !important; }
            .auth-logo { font-size: 28px !important; }
            .auth-title { font-size: 26px !important; }
            .form-input { padding: 12px 14px !important; font-size: 13px !important; }
            .btn { width: 100%; justify-content: center; padding: 14px 20px !important; }
            .auth-form-grid { grid-template-columns: 1fr !important; }
        }
    </style>
    <div class="auth-card">
        <div class="auth-header">
            <div class="auth-logo">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 2C8 2 4 5.5 4 10c0 6 8 12 8 12s8-6 8-12c0-4.5-4-8-8-8z"/>
                    <circle cx="12" cy="10" r="2.5" fill="white" stroke="none"/>
                </svg>
            </div>
            <h1 class="auth-title">Create Account</h1>
            <p class="auth-subtitle">Join Pagkainang Sambayanan today</p>
            <div class="auth-header-line"></div>
        </div>

        <div class="auth-body">
            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="form-group">
                    <label class="form-label" for="name">Full Name</label>
                    <input id="name" type="text" name="name" class="form-input"
                        placeholder="e.g., Juan dela Cruz"
                        value="{{ old('name') }}" required autofocus autocomplete="name">
                    @error('name') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">Email Address</label>
                    <input id="email" type="email" name="email" class="form-input"
                        placeholder="e.g., juan@email.com"
                        value="{{ old('email') }}" required autocomplete="username">
                    @error('email') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="auth-form-grid" style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                    <div class="form-group">
                        <label class="form-label" for="password">Password</label>
                        <input id="password" type="password" name="password" class="form-input"
                            placeholder="Min. 8 characters"
                            required autocomplete="new-password">
                        @error('password') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="password_confirmation">Confirm</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" class="form-input"
                            placeholder="Re-enter password"
                            required autocomplete="new-password">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="role">Register As</label>
                    <select id="role" name="role" class="form-input" required>
                        <option value="" disabled {{ old('role') ? '' : 'selected' }}>Choose your role</option>
                        <option value="donor" {{ old('role') === 'donor' ? 'selected' : '' }}>Donor (Restaurant, Cafe, Individual)</option>
                        <option value="charity" {{ old('role') === 'charity' ? 'selected' : '' }}>Charity (NGO, Relief Center, Barangay)</option>
                    </select>
                    @error('role') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <button type="submit" class="btn-auth" style="margin-top:8px;">
                    Create Account
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </button>
            </form>
        </div>

        <div class="auth-footer-links">
            <p style="font-size:13px;color:var(--muted);margin-bottom:10px;">
                Already have an account? <a href="{{ route('login') }}" class="link-gold">Log in here</a>
            </p>
            <a href="{{ url('/') }}" class="link-muted">← Back to Home</a>
        </div>
    </div>
</x-guest-layout>