<x-app-layout>
    <style>
        .donation-create-grid { display: grid; grid-template-columns: 1fr 380px; gap: 32px; align-items: start; }
        .donation-form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        @media (max-width: 1024px) {
            .donation-create-grid { grid-template-columns: 1fr !important; }
        }
        @media (max-width: 640px) {
            .donation-form-grid { grid-template-columns: 1fr !important; }
        }
    </style>
    <x-slot name="header">Post a New Donation</x-slot>

    <div class="donation-create-grid" style="display:grid;grid-template-columns:1fr 380px;gap:32px;align-items:start;">

        {{-- MAIN FORM --}}
        <div class="card" style="padding:44px;">
            <div style="margin-bottom:36px;">
                <div class="section-label">Share Food</div>
                <h2 style="font-family:'Cormorant Garamond',serif;font-size:32px;font-weight:700;color:var(--dark);margin-bottom:10px;line-height:1.1;">Post a Food Donation</h2>
                <p style="font-size:14px;color:var(--muted);line-height:1.7;">Fill in the details below to share surplus food with communities in need.</p>
            </div>

            <form method="POST" action="{{ route('donations.store') }}">
                @csrf

                {{-- Food Description --}}
                <div class="form-group">
                    <label class="form-label" for="description">Food Description <span style="color:var(--red);">*</span></label>
                    <textarea
                        id="description"
                        name="description"
                        rows="4"
                        class="form-input"
                        placeholder="e.g., Surplus vegetables from lunch service, freshly cooked adobo and rice..."
                        style="resize:vertical;"
                    >{{ old('description') }}</textarea>
                    @error('description') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                {{-- Target Audience --}}
                <div class="form-group">
                    <label class="form-label" for="target_audience">Target Audience <span style="color:var(--red);">*</span></label>
                    <select id="target_audience" name="target_audience" class="form-input">
                        <option value="" disabled {{ old('target_audience') ? '' : 'selected' }}>Select who this food is most suitable for</option>
                        @foreach(['General Families','Children','Seniors','Disaster Victims','Street Dwellers','Persons with Disability'] as $audience)
                            <option value="{{ $audience }}" {{ old('target_audience') === $audience ? 'selected' : '' }}>{{ $audience }}</option>
                        @endforeach
                    </select>
                    @error('target_audience') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                {{-- Affected Area - Show during Emergency Mode --}}
                @if($emergencyMode)
                <div class="form-group" style="padding:20px;background:rgba(176,40,24,0.05);border:1px solid rgba(176,40,24,0.15);border-radius:12px;">
                    <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px;">
                        <svg width="18" height="18" fill="none" stroke="var(--red)" stroke-width="2" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span style="font-weight:700;color:var(--red);font-size:13px;">Disaster Area Tagging</span>
                    </div>
                    
                    <label class="form-label" for="affected_area">
                        Affected Area / Disaster Type
                        <span style="font-weight:400;text-transform:none;letter-spacing:0;color:var(--muted);font-size:11px;">(optional)</span>
                    </label>
                    <div style="position:relative;">
                        <input 
                            type="text" 
                            id="affected_area" 
                            name="affected_area" 
                            class="form-input" 
                            list="area_suggestions"
                            placeholder="e.g., Typhoon Carina victims, Earthquake relief, Flood affected areas"
                            value="{{ old('affected_area') }}"
                            autocomplete="off"
                        >
                        <datalist id="area_suggestions">
                            @foreach(['Typhoon Carina (2024)','Typhoon Egay (2023)','Typhoon Odette (2021)','Central Luzon Floods','Mindanao Earthquake','Batanes Typhoon','Cagayan Valley Floods','NCR Flooding','Batangas Volcanic Ash','Other Disaster Relief'] as $area)
                                <option value="{{ $area }}">
                            @endforeach
                        </datalist>
                    </div>
                    <p style="font-size:12px;color:var(--muted);margin-top:6px;">
                        Tag which disaster area or emergency situation this donation is for.
                    </p>
                    @error('affected_area') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                @endif

                {{-- Charity Picker --}}
                <div class="form-group">
                    <label class="form-label" for="charity_id">
                        Donate To a Specific Charity
                        <span style="font-weight:400;text-transform:none;letter-spacing:0;color:var(--muted);font-size:11px;">(optional)</span>
                    </label>

                    @if($charities->isEmpty())
                        <div style="padding:14px 16px;border-radius:10px;border:1.5px dashed rgba(196,144,16,0.3);background:rgba(196,144,16,0.04);font-size:13px;color:var(--muted);">
                            No verified charities available yet. Your donation will be listed publicly for any charity to claim.
                        </div>
                    @else
                        <select id="charity_id" name="charity_id" class="form-input">
                            <option value="">— No specific charity (open to all) —</option>
                            @foreach($charities as $charity)
                                <option value="{{ $charity->id }}"
                                    {{ old('charity_id', request('charity_id')) == $charity->id ? 'selected' : '' }}>
                                    {{ $charity->name }}
                                </option>
                            @endforeach
                        </select>
                        <p style="font-size:12px;color:var(--muted);margin-top:6px;">
                            Selecting a charity directs your donation specifically to them.
                        </p>
                    @endif
                    @error('charity_id') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                {{-- Emergency Mode Fields: Area Severity --}}
                @php
                    $emergencyMode = \Illuminate\Support\Facades\DB::table('settings')->where('key', 'emergency_mode')->value('value') === '1';
                @endphp
                @if($emergencyMode)
                <div class="form-group" style="padding:20px;background:rgba(176,40,24,0.05);border:1px solid rgba(176,40,24,0.15);border-radius:12px;">
                    <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px;">
                        <svg width="18" height="18" fill="none" stroke="var(--red)" stroke-width="2" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span style="font-weight:700;color:var(--red);font-size:13px;">Emergency Mode Active</span>
                    </div>
                    
                    <div class="donation-form-grid" style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                        <div>
                            <label class="form-label" for="area_severity">Area Severity</label>
                            <select id="area_severity" name="area_severity" class="form-input">
                                <option value="1" {{ old('area_severity') == 1 ? 'selected' : '' }}>1 - Low</option>
                                <option value="2" {{ old('area_severity') == 2 ? 'selected' : '' }}>2 - Medium</option>
                                <option value="3" {{ old('area_severity') == 3 ? 'selected' : '' }}>3 - High</option>
                                <option value="4" {{ old('area_severity') == 4 ? 'selected' : '' }}>4 - Critical</option>
                            </select>
                            <p style="font-size:11px;color:var(--muted);margin-top:4px;">How urgent is this for the affected area?</p>
                        </div>
                        <div>
                            <label class="form-label" for="expires_at">Expiry Time</label>
                            <input type="datetime-local" id="expires_at" name="expires_at" class="form-input" value="{{ old('expires_at') }}">
                            <p style="font-size:11px;color:var(--muted);margin-top:4px;">When does this food expire?</p>
                        </div>
                    </div>
                </div>
                @endif

                <div style="display:flex;align-items:center;gap:16px;margin-top:36px;padding-top:28px;border-top:1px solid rgba(196,144,16,0.12);">
                    <button type="submit" class="btn btn-red" style="padding:14px 36px;">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        Post Donation
                    </button>
                    <a href="{{ route('dashboard') }}" class="btn btn-ghost" style="padding:14px 28px;">Cancel</a>
                </div>
            </form>
        </div>

        {{-- SIDEBAR --}}
        <div style="display:flex;flex-direction:column;gap:20px;position:sticky;top:100px;">

            {{-- Tips --}}
            <div class="card" style="padding:28px;border-top:3px solid var(--gold);">
                <div style="width:44px;height:44px;background:linear-gradient(135deg,var(--gold),var(--gold2));border-radius:12px;display:flex;align-items:center;justify-content:center;margin-bottom:16px;box-shadow:0 4px 14px rgba(196,144,16,0.3);">
                    <svg width="20" height="20" fill="none" stroke="var(--dark)" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                </div>
                <h4 style="font-family:'Cormorant Garamond',serif;font-size:18px;font-weight:700;color:var(--dark);margin-bottom:12px;">Tips for a Good Post</h4>
                <ul style="list-style:none;display:flex;flex-direction:column;gap:10px;">
                    @foreach([
                        'Be specific about the type and quantity of food',
                        'Mention if food is cooked or uncooked',
                        'Include any allergen information if known',
                        'Choose the most appropriate target audience',
                        'Select a specific charity if you have one in mind',
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

            {{-- Charity Requests shortcut --}}
            <div class="card" style="padding:28px;border-top:3px solid var(--red);">
                <div style="width:44px;height:44px;background:linear-gradient(135deg,var(--red),var(--red2));border-radius:12px;display:flex;align-items:center;justify-content:center;margin-bottom:16px;box-shadow:0 4px 14px rgba(176,40,24,0.25);">
                    <svg width="20" height="20" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
                </div>
                <h4 style="font-family:'Cormorant Garamond',serif;font-size:18px;font-weight:700;color:var(--dark);margin-bottom:8px;">See Charity Requests</h4>
                <p style="font-size:13px;color:var(--muted);line-height:1.7;margin-bottom:16px;">View what food charities are currently requesting so you can donate exactly what's needed.</p>
                <a href="{{ route('charity.requests.index') }}" class="btn btn-outline-red" style="width:100%;justify-content:center;padding:11px;">
                    Browse Requests
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </a>
            </div>

            {{-- Quote --}}
            <div style="background:linear-gradient(135deg,var(--dark),var(--dark3));border-radius:16px;padding:28px;color:white;">
                <div style="font-family:'Cormorant Garamond',serif;font-size:20px;font-weight:700;color:var(--gold3);margin-bottom:-4px;">Every meal matters.</div>
                <p style="font-size:13px;color:rgba(255,255,255,0.5);line-height:1.7;">Your donation directly reaches families, children, and communities who need it most.</p>
            </div>

        </div>
    </div>

</x-app-layout>