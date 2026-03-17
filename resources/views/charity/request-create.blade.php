<x-app-layout>
    <x-slot name="header">Post a Food Request</x-slot>

    <div style="max-width:680px;margin:0 auto;">

        {{-- Page Intro --}}
        <div style="margin-bottom:40px;">
            <div class="section-label">Charity Request</div>
            <h2 style="font-family:'Cormorant Garamond',serif;font-size:38px;font-weight:700;color:var(--dark);line-height:1.1;margin-bottom:12px;">
                What Food Do You Need?
            </h2>
            <p style="font-size:15px;color:var(--muted);line-height:1.8;">
                Post a food request so donors can see what your organization needs. Be specific so donors can prepare the right food.
            </p>
        </div>

        {{-- Form Card --}}
        <div class="card" style="padding:40px;">

            @if($errors->any())
                <div class="alert alert-error" style="margin-bottom:28px;">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    <div>
                        @foreach($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('charity.request.store') }}">
                @csrf

                {{-- Food Name --}}
                <div class="form-group">
                    <label class="form-label">Food Name / Type <span style="color:var(--red);">*</span></label>
                    <input
                        type="text"
                        name="food_name"
                        class="form-input"
                        placeholder="e.g. Cooked rice, Canned goods, Bread..."
                        value="{{ old('food_name') }}"
                        required
                    >
                    <div class="form-error">{{ $errors->first('food_name') }}</div>
                </div>

                {{-- Quantity --}}
                <div class="form-group">
                    <label class="form-label">Quantity Needed</label>
                    <input
                        type="text"
                        name="quantity"
                        class="form-input"
                        placeholder="e.g. 50 packs, 10 kg, 100 servings..."
                        value="{{ old('quantity') }}"
                    >
                    <div class="form-error">{{ $errors->first('quantity') }}</div>
                </div>

                {{-- Urgency --}}
                <div class="form-group">
                    <label class="form-label">Urgency Level <span style="color:var(--red);">*</span></label>
                    <select name="urgency" class="form-input" required>
                        <option value="">Select urgency level...</option>
                        <option value="normal"   {{ old('urgency') === 'normal'   ? 'selected' : '' }}>Normal — We need this within the week</option>
                        <option value="urgent"   {{ old('urgency') === 'urgent'   ? 'selected' : '' }}>Urgent — We need this within 1–2 days</option>
                        <option value="critical" {{ old('urgency') === 'critical' ? 'selected' : '' }}>Critical — We need this today</option>
                    </select>
                    <div class="form-error">{{ $errors->first('urgency') }}</div>
                </div>

                {{-- Description --}}
                <div class="form-group">
                    <label class="form-label">Additional Details</label>
                    <textarea
                        name="description"
                        class="form-input"
                        rows="4"
                        placeholder="Describe who will receive this food, any dietary requirements, or other relevant details..."
                        style="resize:vertical;"
                    >{{ old('description') }}</textarea>
                    <div class="form-error">{{ $errors->first('description') }}</div>
                </div>

                {{-- Info box --}}
                <div style="background:rgba(196,144,16,0.06);border:1px solid rgba(196,144,16,0.2);border-radius:10px;padding:16px 18px;margin-bottom:28px;display:flex;gap:12px;align-items:flex-start;">
                    <svg width="16" height="16" fill="none" stroke="var(--gold)" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0;margin-top:2px;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                    <p style="font-size:12px;color:var(--muted);line-height:1.7;margin:0;">
                        Your request will be visible to all donors on the platform. Once a donor fulfills your request, it will be marked as fulfilled automatically.
                    </p>
                </div>

                {{-- Submit --}}
                <div style="display:flex;gap:12px;">
                    <button type="submit" class="btn btn-red" style="flex:1;justify-content:center;padding:15px;">
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        Post Food Request
                    </button>
                    <a href="{{ route('dashboard') }}" class="btn btn-ghost" style="padding:15px 24px;">
                        Cancel
                    </a>
                </div>

            </form>
        </div>
    </div>

</x-app-layout>