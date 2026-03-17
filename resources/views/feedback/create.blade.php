<x-app-layout>
    <style>
        .feedback-create-grid { display: grid; grid-template-columns: 1fr 360px; gap: 32px; align-items: start; }
        .star-ratings-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 28px; padding-top: 28px; border-top: 1px solid rgba(196,144,16,0.12); }
        @media (max-width: 1024px) {
            .feedback-create-grid { grid-template-columns: 1fr !important; }
        }
        @media (max-width: 640px) {
            .star-ratings-grid { grid-template-columns: 1fr !important; }
        }
    </style>
    <x-slot name="header">Submit Feedback</x-slot>

    <div class="feedback-create-grid" style="display:grid;grid-template-columns:1fr 360px;gap:32px;align-items:start;">

        {{-- MAIN FORM --}}
        <div class="card" style="padding:44px;">
            <div style="margin-bottom:36px;">
                <div class="section-label">Proof of Distribution</div>
                <h2 style="font-family:'Cormorant Garamond',serif;font-size:32px;font-weight:700;color:var(--dark);margin-bottom:10px;line-height:1.1;">Submit Feedback</h2>
                <p style="font-size:14px;color:var(--muted);line-height:1.7;">Share proof that the donated food reached those in need.</p>
            </div>

            {{-- Donation reference --}}
            <div style="background:var(--lighter);border:1px solid rgba(196,144,16,0.15);border-radius:12px;padding:16px 20px;margin-bottom:32px;display:flex;align-items:center;gap:14px;">
                <div style="width:40px;height:40px;background:linear-gradient(135deg,var(--red),var(--red2));border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg width="18" height="18" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24"><path d="M18 8h1a4 4 0 010 8h-1"/><path d="M2 8h16v9a4 4 0 01-4 4H6a4 4 0 01-4-4V8z"/><line x1="6" y1="1" x2="6" y2="4"/><line x1="10" y1="1" x2="10" y2="4"/><line x1="14" y1="1" x2="14" y2="4"/></svg>
                </div>
                <div>
                    <div style="font-size:10px;font-weight:700;letter-spacing:1.2px;text-transform:uppercase;color:var(--muted);margin-bottom:3px;">Donation</div>
                    <div style="font-size:14px;font-weight:700;color:var(--dark);">{{ $donation->description }}</div>
                    <div style="font-size:12px;color:var(--muted);margin-top:2px;">Posted by {{ $donation->donor->name }} &middot; {{ $donation->created_at->format('M d, Y') }}</div>
                </div>
            </div>

            <form method="POST" action="{{ route('feedback.store', $donation->id) }}" enctype="multipart/form-data">
                @csrf

                <div class="form-group">
                    <label class="form-label" for="message">Distribution Report</label>
                    <textarea
                        id="message"
                        name="message"
                        rows="5"
                        class="form-input"
                        placeholder="Describe how the food was distributed — who received it, how many people were served, and where..."
                        style="resize:vertical;"
                    >{{ old('message') }}</textarea>
                    @error('message') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="photo">Proof Photo <span style="font-weight:400;color:var(--muted);font-size:12px;">(optional)</span></label>
                    <div style="border:2px dashed rgba(196,144,16,0.25);border-radius:12px;padding:28px;text-align:center;background:var(--lighter);cursor:pointer;transition:border-color 0.2s;"
                         onclick="document.getElementById('photo').click()"
                         ondragover="event.preventDefault();this.style.borderColor='var(--gold)'"
                         ondragleave="this.style.borderColor='rgba(196,144,16,0.25)'"
                         ondrop="event.preventDefault();this.style.borderColor='rgba(196,144,16,0.25)';handleDrop(event)">
                        <div id="photo-preview" style="display:none;margin-bottom:12px;">
                            <img id="preview-img" style="max-height:180px;border-radius:8px;object-fit:cover;">
                        </div>
                        <div id="photo-placeholder">
                            <div style="width:48px;height:48px;background:rgba(196,144,16,0.1);border-radius:12px;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;">
                                <svg width="22" height="22" fill="none" stroke="var(--gold)" stroke-width="1.5" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                            </div>
                            <p style="font-size:13px;font-weight:600;color:var(--dark);margin-bottom:4px;">Click or drag a photo here</p>
                            <p style="font-size:12px;color:var(--muted);">JPG, PNG up to 5MB</p>
                        </div>
                    </div>
                    <input type="file" id="photo" name="photo" accept="image/*" style="display:none;" onchange="previewPhoto(this)">
                    @error('photo') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                {{-- Star Ratings --}}
                <div class="star-ratings-grid" style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-top:28px;padding-top:28px;border-top:1px solid rgba(196,144,16,0.12);">
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">Food Quality Rating</label>
                        <div class="star-rating" id="food-quality-rating">
                            @for($i = 1; $i <= 5; $i++)
                                <button type="button" class="star-btn" data-value="{{ $i }}" onclick="setRating('food_quality_rating', {{ $i }})">
                                    <svg class="star-icon" width="28" height="28" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                </button>
                            @endfor
                        </div>
                        <input type="hidden" name="food_quality_rating" id="food_quality_rating" value="">
                        <p style="font-size:12px;color:var(--muted);margin-top:6px;">Rate the quality of the donated food</p>
                        @error('food_quality_rating') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">Quantity Accuracy Rating</label>
                        <div class="star-rating" id="quantity-rating">
                            @for($i = 1; $i <= 5; $i++)
                                <button type="button" class="star-btn" data-value="{{ $i }}" onclick="setRating('quantity_rating', {{ $i }})">
                                    <svg class="star-icon" width="28" height="28" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                </button>
                            @endfor
                        </div>
                        <input type="hidden" name="quantity_rating" id="quantity_rating" value="">
                        <p style="font-size:12px;color:var(--muted);margin-top:6px;">Rate if quantity matched the description</p>
                        @error('quantity_rating') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div style="display:flex;align-items:center;gap:16px;margin-top:36px;padding-top:28px;border-top:1px solid rgba(196,144,16,0.12);">
                    <button type="submit" class="btn btn-red" style="padding:14px 36px;">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 2L11 13"/><path d="M22 2L15 22 11 13 2 9l20-7z"/></svg>
                        Submit Feedback
                    </button>
                    <a href="{{ route('donations.available') }}" class="btn btn-ghost" style="padding:14px 28px;">Cancel</a>
                </div>
            </form>
        </div>

        {{-- SIDEBAR --}}
        <div style="display:flex;flex-direction:column;gap:20px;position:sticky;top:100px;">

            <div class="card" style="padding:28px;border-top:3px solid var(--gold);">
                <div style="width:44px;height:44px;background:linear-gradient(135deg,var(--gold),var(--gold2));border-radius:12px;display:flex;align-items:center;justify-content:center;margin-bottom:16px;box-shadow:0 4px 14px rgba(196,144,16,0.3);">
                    <svg width="20" height="20" fill="none" stroke="var(--dark)" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                </div>
                <h4 style="font-family:'Cormorant Garamond',serif;font-size:18px;font-weight:700;color:var(--dark);margin-bottom:12px;">What to Include</h4>
                <ul style="list-style:none;display:flex;flex-direction:column;gap:10px;">
                    @foreach([
                        'Number of people or families served',
                        'Location where food was distributed',
                        'Date and time of distribution',
                        'Any special notes about the recipients',
                    ] as $tip)
                    <li style="display:flex;align-items:flex-start;gap:10px;font-size:13px;color:var(--muted);line-height:1.6;">
                        <div style="width:18px;height:18px;border-radius:50%;background:rgba(196,144,16,0.15);display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:1px;">
                            <svg width="10" height="10" fill="none" stroke="var(--gold)" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                        </div>
                        {{ $tip }}
                    </li>
                    @endforeach
                </ul>
            </div>

            <div style="background:linear-gradient(135deg,var(--dark),var(--dark3));border-radius:16px;padding:28px;color:white;">
                <div style="font-family:'Cormorant Garamond',serif;font-size:20px;font-weight:700;color:var(--gold3);margin-bottom:8px;">Your report matters.</div>
                <p style="font-size:13px;color:rgba(255,255,255,0.5);line-height:1.7;">Feedback builds trust between donors and charities — and helps more food reach those who need it.</p>
            </div>

        </div>
    </div>

    <script>
        function previewPhoto(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = e => {
                    document.getElementById('preview-img').src = e.target.result;
                    document.getElementById('photo-preview').style.display = 'block';
                    document.getElementById('photo-placeholder').style.display = 'none';
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
        function handleDrop(e) {
            const file = e.dataTransfer.files[0];
            if (file && file.type.startsWith('image/')) {
                const input = document.getElementById('photo');
                const dt = new DataTransfer();
                dt.items.add(file);
                input.files = dt.files;
                previewPhoto(input);
            }
        }
        function setRating(inputName, value) {
            document.getElementById(inputName).value = value;
            const containerId = inputName === 'food_quality_rating' ? 'food-quality-rating' : 'quantity-rating';
            const container = document.getElementById(containerId);
            const stars = container.querySelectorAll('.star-btn');
            stars.forEach((star, index) => {
                if (index < value) {
                    star.querySelector('.star-icon').style.color = 'var(--gold)';
                    star.querySelector('.star-icon').style.fill = 'var(--gold)';
                } else {
                    star.querySelector('.star-icon').style.color = 'var(--muted)';
                    star.querySelector('.star-icon').style.fill = 'none';
                }
            });
        }
    </script>
    <style>
        .star-rating {
            display: flex;
            gap: 4px;

        }
        .star-btn {
            background: none;
            border: solid 2px var(--gold);
            padding: 2px;
            cursor: pointer;
            transition: transform 0.15s;
        }
        .star-btn:hover {
            transform: scale(1.15);
        }
        .star-icon {
            color: var(--muted);
            fill: none;
            transition: color 0.15s, fill 0.15s;
        }
    </style>

</x-app-layout> 