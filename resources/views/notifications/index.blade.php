<x-app-layout>
    <x-slot name="header">Notifications</x-slot>

    <div style="margin-bottom:40px;">
        <div class="section-label">Updates</div>
        <h2 style="font-family:'Cormorant Garamond',serif;font-size:38px;font-weight:700;color:var(--dark);line-height:1.1;margin-bottom:12px;">Notifications</h2>
        <p style="font-size:15px;color:var(--muted);line-height:1.8;">Stay updated on your donations, requests, and feedback.</p>
    </div>

    @if($notifications->isEmpty())
        <div class="card" style="padding:80px 40px;text-align:center;">
            <div style="width:64px;height:64px;background:rgba(196,144,16,0.08);border-radius:16px;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
                <svg width="28" height="28" fill="none" stroke="var(--gold)" stroke-width="1.5" viewBox="0 0 24 24"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 01-3.46 0"/></svg>
            </div>
            <h3 style="font-family:'Cormorant Garamond',serif;font-size:24px;font-weight:700;color:var(--dark);margin-bottom:8px;">No notifications yet</h3>
            <p style="font-size:14px;color:var(--muted);">No notifications yet. You'll be notified about donation requests, accepted donations, and feedback.</p>
        </div>
    @else
        <div style="display:flex;flex-direction:column;gap:12px;">
            @foreach($notifications as $notif)
            <div class="card" style="padding:20px 24px;display:flex;align-items:center;gap:16px;border-left:4px solid {{ $notif->is_read ? 'rgba(196,144,16,0.2)' : 'var(--red)' }};">
                <div style="width:44px;height:44px;border-radius:12px;background:{{ $notif->is_read ? 'rgba(196,144,16,0.08)' : 'linear-gradient(135deg,var(--red),var(--red2))' }};display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg width="20" height="20" fill="none" stroke="{{ $notif->is_read ? 'var(--gold)' : 'white' }}" stroke-width="2" viewBox="0 0 24 24"><path d="M18 8h1a4 4 0 010 8h-1"/><path d="M2 8h16v9a4 4 0 01-4 4H6a4 4 0 01-4-4V8z"/><line x1="6" y1="1" x2="6" y2="4"/><line x1="10" y1="1" x2="10" y2="4"/><line x1="14" y1="1" x2="14" y2="4"/></svg>
                </div>
                <div style="flex:1;">
                    <p style="font-size:14px;color:var(--dark);font-weight:{{ $notif->is_read ? '400' : '600' }};margin-bottom:4px;line-height:1.6;">
                        {{ $notif->message }}
                    </p>
                    <span style="font-size:12px;color:var(--muted);">{{ $notif->created_at->diffForHumans() }}</span>
                </div>
                @if(!$notif->is_read)
                    <div style="width:8px;height:8px;border-radius:50%;background:var(--red);flex-shrink:0;"></div>
                @endif
            </div>
            @endforeach
        </div>
    @endif

</x-app-layout>