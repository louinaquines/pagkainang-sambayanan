<x-guest-layout>
    <div class="auth-card">
        <div class="auth-header">
            <div class="auth-logo">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                    <path d="M7 11V7a5 5 0 0110 0v4"/>
                </svg>
            </div>
            <h1 class="auth-title">Forgot Password?</h1>
            <p class="auth-subtitle">We'll send you a reset link</p>
            <div class="auth-header-line"></div>
        </div>

        <div class="auth-body">
            @if (session('status'))
                <div class="alert-success">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                    {{ session('status') }}
                </div>
            @endif

            <div class="info-box">
                Enter your registered email address and we'll send you a link to reset your password.
            </div>

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <div class="form-group">
                    <label class="form-label" for="email">Email Address</label>
                    <input id="email" type="email" name="email" class="form-input"
                        placeholder="e.g., juan@email.com"
                        value="{{ old('email') }}" required autofocus>
                    @error('email') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <button type="submit" class="btn-auth">
                    Send Reset Link
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </button>
            </form>
        </div>

        <div class="auth-footer-links">
            <a href="{{ route('login') }}" class="link-muted">← Back to Login</a>
        </div>
    </div>
</x-guest-layout>