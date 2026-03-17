<x-guest-layout>
    <style>
        @media (max-width: 640px) {
            .auth-card { padding: 24px 20px !important; }
            .auth-logo { font-size: 28px !important; }
            .auth-title { font-size: 26px !important; }
            .form-input { padding: 12px 14px !important; font-size: 13px !important; }
            .btn { width: 100%; justify-content: center; padding: 14px 20px !important; }
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
            <h1 class="auth-title">Welcome Back!</h1>
            <p class="auth-subtitle">Log in to Pagkainang Sambayanan</p>
            <div class="auth-header-line"></div>
        </div>

        <div class="auth-body">
            {{-- Session Status --}}
            @if (session('status'))
                <div class="alert-success">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="form-group">
                    <label class="form-label" for="email">Email Address</label>
                    <input id="email" type="email" name="email" class="form-input"
                        placeholder="e.g., juan@email.com"
                        value="{{ old('email') }}" required autofocus autocomplete="username">
                    @error('email') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <input id="password" type="password" name="password" class="form-input"
                        placeholder="Enter your password"
                        required autocomplete="current-password">
                    @error('password') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;">
                    <div class="check-wrap">
                        <input type="checkbox" id="remember_me" name="remember">
                        <label class="check-label" for="remember_me">Remember me</label>
                    </div>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="link-gold" style="font-size:13px;">Forgot password?</a>
                    @endif
                </div>

                <button type="submit" class="btn-auth">
                    Log In
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </button>
            </form>
        </div>

        <div class="auth-footer-links">
            <p style="font-size:13px;color:var(--muted);margin-bottom:10px;">
                Don't have an account? <a href="{{ route('register') }}" class="link-gold">Register here</a>
            </p>
            <a href="{{ url('/') }}" class="link-muted">← Back to Home</a>
        </div>
    </div>

    @if(session('registered'))
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        Swal.fire({
            icon:'success',
            title:'Registration Successful!',
            text:'Your account has been created. Please log in.',
            confirmButtonColor:'#B02818',
            confirmButtonText:'Log In Now',
        });
    </script>
    @endif
</x-guest-layout>