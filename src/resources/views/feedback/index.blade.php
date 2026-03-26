<x-app-layout>
    <style>
        .feedback-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; }
        .ratings-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        @media (max-width: 768px) {
            .feedback-grid { grid-template-columns: 1fr !important; }
            .ratings-grid { grid-template-columns: 1fr !important; }
        }
    </style>
    <x-slot name="header">Feedback Log</x-slot>

    @php $user = auth()->user(); @endphp

    <div style="display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:36px;flex-wrap:wrap;gap:16px;">
        <div>
            <div class="section-label">Transparency</div>
            <h2 style="font-family:'Cormorant Garamond',serif;font-size:32px;font-weight:700;color:var(--dark);line-height:1.1;">Feedback Log</h2>
            <p style="font-size:14px;color:var(--muted);margin-top:8px;">
                @if($user->role === 'donor') Proof of distribution submitted by charities for your donations.
                @elseif($user->role === 'charity') Your feedback reports and donations awaiting your feedback.
                @else Proof of food distribution submitted by verified charities.
                @endif
            </p>
        </div>
        <div style="background:var(--white);border:1px solid rgba(196,144,16,0.15);border-radius:12px;padding:12px 24px;text-align:center;">
            <div style="font-family:'Cormorant Garamond',serif;font-size:32px;font-weight:700;color:var(--red);line-height:1;">{{ $feedbacks->count() }}</div>
            <div style="font-size:10px;color:var(--muted);text-transform:uppercase;letter-spacing:1.5px;margin-top:4px;font-weight:600;">Total Reports</div>
        </div>
    </div>

    {{-- ââ CHARITY: Donations waiting for feedback ââ --}}
    @if($user->role === 'charity' && isset($pendingDonations) && $pendingDonations->isNotEmpty())
        <div style="margin-bottom:40px;">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px;">
                <div style="width:8px;height:8px;background:var(--gold3);border-radius:50%;box-shadow:0 0 6px rgba(240,200,64,0.7);"></div>
                <span style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:1.5px;color:var(--gold);">Awaiting Your Feedback</span>
            </div>
            <div class="feedback-grid" style="display:grid;grid-template-columns:repeat(2,1fr);gap:20px;">
                @foreach($pendingDonations as $donation)
                <div class="card" style="padding:20px 24px;border-left:4px solid var(--gold);">
                    <div style="font-size:15px;font-weight:700;color:var(--dark);margin-bottom:6px;">{{ $donation->description }}</div>
                    <div style="font-size:12px;color:var(--muted);margin-bottom:4px;">
                        Donor: <strong style="color:var(--text);">{{ $donation->donor->name ?? 'Unknown' }}</strong>
                        &nbsp;&middot;&nbsp; {{ $donation->created_at->format('M d, Y') }}
                    </div>
                    <div style="margin-top:14px;">
                        <a href="{{ route('feedback.create', $donation->id) }}"
                           class="btn btn-gold"
                           style="padding:9px 20px;font-size:12px;display:inline-flex;align-items:center;gap:8px;">
                            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                            Write Feedback / Comment to Donor
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- ââ SUBMITTED FEEDBACKS ââ --}}
    @if($feedbacks->isEmpty())
        <div class="card" style="padding:80px 40px;text-align:center;">
            <div style="width:72px;height:72px;background:rgba(196,144,16,0.08);border-radius:20px;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
                <svg width="32" height="32" fill="none" stroke="var(--gold)" stroke-width="1.5" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
            </div>
            <h3 style="font-family:'Cormorant Garamond',serif;font-size:24px;font-weight:700;color:var(--dark);margin-bottom:8px;">No feedback yet</h3>
            <p style="font-size:14px;color:var(--muted);">
                @if($user->role === 'charity') Use the buttons above to write feedback for a received donation.
                @else Feedback will appear here once charities submit proof of food distribution.
                @endif
            </p>
        </div>
    @else
        @if($user->role === 'charity')
            <div style="margin-bottom:16px;">
                <span style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:1.5px;color:var(--muted);">Submitted Feedback Reports</span>
            </div>
        @endif

        <div class="feedback-grid" style="display:grid;grid-template-columns:repeat(2,1fr);gap:24px;">
            @foreach($feedbacks as $fb)
            <div class="card" style="padding:0;overflow:hidden;">

                {{-- Photo --}}
                @if($fb->photo_path)
                <div style="height:200px;overflow:hidden;background:var(--light);">
                    <img src="{{ Storage::url($fb->photo_path) }}"
                         alt="Feedback photo"
                         style="width:100%;height:100%;object-fit:cover;transition:transform 0.4s ease;"
                         onmouseover="this.style.transform='scale(1.04)'"
                         onmouseout="this.style.transform='scale(1)'">
                </div>
                @else
                <div style="height:80px;background:linear-gradient(135deg,var(--lighter),var(--light));display:flex;align-items:center;justify-content:center;">
                    <svg width="28" height="28" fill="none" stroke="rgba(196,144,16,0.25)" stroke-width="1.5" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                </div>
                @endif

                <div style="padding:20px 24px;">

                    @if($fb->donation && $fb->donation->donor)
                    <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:var(--muted);margin-bottom:6px;">Donation by Donor</div>
                    <div style="font-size:13px;font-weight:700;color:var(--dark);margin-bottom:12px;">
                        {{ $fb->donation->donor->name }}
                        <span style="font-weight:400;color:var(--muted);margin-left:4px;">-</span>
                        <span style="font-weight:400;color:var(--muted);">{{ \Illuminate\Support\Str::limit($fb->donation->description, 45) }}</span>
                    </div>
                    @endif

                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;flex-wrap:wrap;gap:8px;">
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:34px;height:34px;background:linear-gradient(135deg,var(--red),var(--red2));border-radius:9px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <svg width="15" height="15" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                            </div>
                            <div>
                                <div style="font-size:13px;font-weight:700;color:var(--dark);">{{ $fb->charity?->name ?? 'Charity' }}</div>
                                <div style="font-size:11px;color:var(--muted);">{{ $fb->charity?->organization_name ?? 'Charity Organization' }}</div>
                            </div>
                        </div>
                        <span style="font-size:11px;color:var(--muted);">{{ $fb->created_at->diffForHumans() }}</span>
                    </div>

                    {{-- Feedback message --}}
                    <div style="background:var(--lighter);border-radius:10px;padding:14px 16px;margin-bottom:14px;border-left:3px solid var(--gold);">
                        <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:var(--gold);margin-bottom:6px;">Distribution Report</div>
                        <p style="font-size:13px;color:var(--text);line-height:1.7;margin:0;font-style:italic;">"{{ $fb->message }}"</p>
                    </div>

                    {{-- Ratings Display --}}
                    @if($fb->food_quality_rating || $fb->quantity_rating)
                        <div class="ratings-grid" style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:14px;">
                            @if($fb->food_quality_rating)
                            <div style="background:rgba(196,144,16,0.08);border-radius:8px;padding:10px 14px;">
                                <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:var(--gold);margin-bottom:4px;">Food Quality</div>
                                <div style="display:flex;gap:2px;">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $fb->food_quality_rating)
                                            <svg width="16" height="16" fill="var(--gold)" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                        @else
                                            <svg width="16" height="16" fill="none" stroke="var(--muted)" stroke-width="1.5" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                        @endif
                                    @endfor
                                </div>
                            </div>
                            @endif
                            @if($fb->quantity_rating)
                            <div style="background:rgba(196,144,16,0.08);border-radius:8px;padding:10px 14px;">
                                <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:var(--gold);margin-bottom:4px;">Quantity Accuracy</div>
                                <div style="display:flex;gap:2px;">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $fb->quantity_rating)
                                            <svg width="16" height="16" fill="var(--gold)" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                        @else
                                            <svg width="16" height="16" fill="none" stroke="var(--muted)" stroke-width="1.5" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                        @endif
                                    @endfor
                                </div>
                            </div>
                            @endif
                        </div>
                    @endif

                    {{-- Additional note (charity_comment) Ã¢ visible to all --}}
                    @if($fb->charity_comment)
                        <div style="background:rgba(26,122,64,0.05);border:1px solid rgba(26,122,64,0.15);border-radius:10px;padding:12px 16px;margin-bottom:12px;">
                            <div style="font-size:10px;font-weight:700;letter-spacing:1.2px;text-transform:uppercase;color:#1A7A40;margin-bottom:6px;">Additional Note from Charity</div>
                            <p style="font-size:13px;color:var(--text);line-height:1.7;margin:0;">{{ $fb->charity_comment }}</p>
                        </div>
                    @endif

                    {{-- Charity: add/edit additional note --}}
                    @if($user->role === 'charity' && $fb->charity_id === $user->id)
                        <details style="margin-top:4px;">
                            <summary style="font-size:12px;font-weight:600;color:var(--gold);cursor:pointer;list-style:none;display:inline-flex;align-items:center;gap:6px;">
                                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                                {{ $fb->charity_comment ? 'Edit additional note to donor' : 'Add additional note to donor' }}
                            </summary>
                            <form method="POST" action="{{ route('feedback.comment', $fb->id) }}" style="margin-top:12px;">
                                @csrf
                                <textarea name="charity_comment" rows="3"
                                    style="width:100%;padding:12px 14px;border:1.5px solid rgba(196,144,16,0.2);border-radius:10px;font-size:13px;font-family:'Jost',sans-serif;color:var(--dark);background:var(--white);resize:vertical;box-sizing:border-box;outline:none;transition:border-color 0.2s;"
                                    onfocus="this.style.borderColor='var(--gold)'" onblur="this.style.borderColor='rgba(196,144,16,0.2)'"
                                    placeholder="Add a comment or extra note for the donor about this distribution...">{{ $fb->charity_comment }}</textarea>
                                <button type="submit" class="btn btn-gold" style="margin-top:10px;padding:9px 20px;font-size:12px;">
                                    Save Note
                                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                                </button>
                            </form>
                        </details>
                    @endif

                </div>
            </div>
            @endforeach
        </div>
    @endif

</x-app-layout>
