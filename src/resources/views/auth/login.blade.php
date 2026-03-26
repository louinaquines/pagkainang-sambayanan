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
                
                <div style="text-align: center; margin: 20px 0; color: var(--muted); font-size: 13px; position: relative;">
                    <span style="background: white; padding: 0 10px; position: relative; z-index: 1;">OR</span>
                    <div style="border-bottom: 1px solid #eee; position: absolute; top: 50%; width: 100%; z-index: 0;"></div>
                </div>

                <a href="{{ url('auth/google') }}" class="btn-auth" style="background: white; border: 1px solid #ddd; color: #444; display: flex; align-items: center; justify-content: center; gap: 10px; text-decoration: none;">
                    <svg width="18" height="18" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                    </svg>
                    Continue with Google
                </a>
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