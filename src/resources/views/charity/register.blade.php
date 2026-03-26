<x-app-layout>
    <style>
        .charity-reg-grid { display: grid; grid-template-columns: 1fr 360px; gap: 32px; align-items: start; }
        .charity-form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        @media (max-width: 1024px) {
            .charity-reg-grid { grid-template-columns: 1fr !important; }
        }
        @media (max-width: 640px) {
            .charity-form-grid { grid-template-columns: 1fr !important; }
            .charity-card { padding: 20px 16px !important; }
        }
    </style>
    <x-slot name="header">Register as Charity</x-slot>

    <div class="charity-reg-grid" style="display:grid;grid-template-columns:1fr 360px;gap:32px;align-items:start;">

        {{-- FORM --}}
        <div class="card" style="padding:44px;">
            <div style="margin-bottom:36px;">
                <div class="section-label">Join the Network</div>
                <h2 style="font-family:'Cormorant Garamond',serif;font-size:32px;font-weight:700;color:var(--dark);margin-bottom:10px;line-height:1.1;">Register Your Organization</h2>
                <p style="font-size:14px;color:var(--muted);line-height:1.7;">Fill out your organization details and upload your legitimacy document. An admin will review and verify your account before you can claim donations.</p>
            </div>

            @if(session('success'))
                <div style="background:#E4F5EB;border:1px solid rgba(26,122,64,0.2);border-radius:10px;padding:14px 20px;margin-bottom:24px;font-size:13px;font-weight:600;color:#1A7A40;display:flex;align-items:center;gap:10px;">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('charity.submit') }}" enctype="multipart/form-data">
                @csrf

                <div class="charity-form-grid" style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">

                    <div class="form-group" style="grid-column:1/-1;">
                        <label class="form-label" for="organization_name">Organization Name</label>
                        <input type="text" id="organization_name" name="organization_name"
                            class="form-input" placeholder="e.g., Damayan Relief Center"
                            value="{{ old('organization_name') }}" required>
                        @error('organization_name') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="form-group" style="grid-column:1/-1;">
                        <label class="form-label" for="organization_description">Organization Description</label>
                        <textarea id="organization_description" name="organization_description"
                            class="form-input" rows="4"
                            placeholder="Describe your organization and who you serve..."
                            style="resize:vertical;">{{ old('organization_description') }}</textarea>
                        @error('organization_description') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="contact_number">Contact Number</label>
                        <input type="text" id="contact_number" name="contact_number"
                            class="form-input" placeholder="e.g., 09171234567"
                            value="{{ old('contact_number') }}" required>
                        @error('contact_number') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="address">Address</label>
                        <input type="text" id="address" name="address"
                            class="form-input" placeholder="e.g., Barangay 123, Quezon City"
                            value="{{ old('address') }}" required>
                        @error('address') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    {{-- Legitimacy Document Upload --}}
                    <div class="form-group" style="grid-column:1/-1;">
                        <label class="form-label" for="legitimacy_document">
                            Legitimacy Document
                            <span style="font-weight:400;color:var(--muted);font-size:12px;"> (required)</span>
                        </label>
                        <p style="font-size:12px;color:var(--muted);margin-bottom:10px;line-height:1.6;">
                            Upload your SEC Certificate of Registration, DTI permit, DSWD accreditation, or any government-issued document that proves your organization's legitimacy.
                        </p>
                        <div id="doc-dropzone"
                             style="border:2px dashed rgba(196,144,16,0.25);border-radius:12px;padding:32px;text-align:center;background:var(--lighter);cursor:pointer;transition:border-color 0.2s,background 0.2s;"
                             onclick="document.getElementById('legitimacy_document').click()"
                             ondragover="event.preventDefault();this.style.borderColor='var(--gold)';this.style.background='rgba(196,144,16,0.05)'"
                             ondragleave="this.style.borderColor='rgba(196,144,16,0.25)';this.style.background='var(--lighter)'"
                             ondrop="event.preventDefault();this.style.borderColor='rgba(196,144,16,0.25)';this.style.background='var(--lighter)';handleDocDrop(event)">

                            <div id="doc-placeholder">
                                <div style="width:52px;height:52px;background:rgba(196,144,16,0.1);border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 14px;">
                                    <svg width="24" height="24" fill="none" stroke="var(--gold)" stroke-width="1.5" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="12" x2="12" y2="18"/><line x1="9" y1="15" x2="15" y2="15"/></svg>
                                </div>
                                <p style="font-size:13px;font-weight:600;color:var(--dark);margin-bottom:4px;">Click or drag your document here</p>
                                <p style="font-size:12px;color:var(--muted);">PDF, JPG, or PNG — max 5MB</p>
                            </div>

                            <div id="doc-selected" style="display:none;align-items:center;gap:14px;justify-content:center;">
                                <div style="width:44px;height:44px;background:rgba(26,122,64,0.1);border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <svg width="20" height="20" fill="none" stroke="#1A7A40" stroke-width="2" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                                </div>
                                <div style="text-align:left;">
                                    <div id="doc-filename" style="font-size:13px;font-weight:700;color:var(--dark);"></div>
                                    <div style="font-size:11px;color:var(--muted);margin-top:2px;">Click to change file</div>
                                </div>
                            </div>
                        </div>
                        <input type="file" id="legitimacy_document" name="legitimacy_document"
                            accept=".pdf,.jpg,.jpeg,.png" style="display:none;"
                            onchange="handleDocSelect(this)" required>
                        @error('legitimacy_document') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                </div>

                <div style="display:flex;align-items:center;gap:16px;margin-top:36px;padding-top:28px;border-top:1px solid rgba(196,144,16,0.12);">
                    <button type="submit" class="btn btn-red" style="padding:14px 36px;">
                        Submit for Verification
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </button>
                    <a href="{{ route('dashboard') }}" class="btn btn-ghost" style="padding:14px 28px;">Cancel</a>
                </div>
            </form>
        </div>

        {{-- SIDEBAR --}}
        <div style="display:flex;flex-direction:column;gap:20px;position:sticky;top:100px;">

            <div class="card" style="padding:28px;border-top:3px solid var(--red);">
                <h4 style="font-family:'Cormorant Garamond',serif;font-size:18px;font-weight:700;color:var(--dark);margin-bottom:16px;">Verification Process</h4>
                <div style="display:flex;flex-direction:column;gap:16px;">
                    @foreach([
                        ['01', 'Submit your organization details and legitimacy document.'],
                        ['02', 'Our admin team reviews your application and document.'],
                        ['03', 'Once approved, you can start claiming food donations.'],
                    ] as [$num, $text])
                    <div style="display:flex;align-items:flex-start;gap:14px;">
                        <div style="width:32px;height:32px;background:linear-gradient(135deg,var(--red),var(--red2));border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:11px;font-weight:800;color:white;letter-spacing:0.5px;">{{ $num }}</div>
                        <p style="font-size:13px;color:var(--muted);line-height:1.6;padding-top:6px;">{{ $text }}</p>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="card" style="padding:28px;border-top:3px solid var(--gold);">
                <h4 style="font-family:'Cormorant Garamond',serif;font-size:16px;font-weight:700;color:var(--dark);margin-bottom:12px;">Accepted Documents</h4>
                <ul style="list-style:none;display:flex;flex-direction:column;gap:10px;">
                    @foreach([
                        'SEC Certificate of Registration',
                        'DTI Business Permit',
                        'DSWD Accreditation',
                        'CDA Certificate (for cooperatives)',
                        'LGU-issued Certificate',
                    ] as $doc)
                    <li style="display:flex;align-items:flex-start;gap:10px;font-size:13px;color:var(--muted);line-height:1.5;">
                        <div style="width:18px;height:18px;border-radius:50%;background:rgba(196,144,16,0.12);display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:1px;">
                            <svg width="10" height="10" fill="none" stroke="var(--gold)" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                        </div>
                        {{ $doc }}
                    </li>
                    @endforeach
                </ul>
            </div>

            <div style="background:linear-gradient(135deg,var(--dark),var(--dark3));border-radius:16px;padding:28px;">
                <div style="font-family:'Cormorant Garamond',serif;font-size:20px;font-weight:700;color:var(--gold3);margin-bottom:8px;">Be the bridge.</div>
                <p style="font-size:13px;color:rgba(255,255,255,0.45);line-height:1.7;">Registered charities connect food donors directly to communities who need it most.</p>
            </div>

        </div>
    </div>

    <script>
        function handleDocSelect(input) {
            if (input.files && input.files[0]) {
                showDocSelected(input.files[0].name);
            }
        }
        function handleDocDrop(e) {
            const file = e.dataTransfer.files[0];
            if (file) {
                const input = document.getElementById('legitimacy_document');
                const dt = new DataTransfer();
                dt.items.add(file);
                input.files = dt.files;
                showDocSelected(file.name);
            }
        }
        function showDocSelected(name) {
            document.getElementById('doc-placeholder').style.display = 'none';
            const sel = document.getElementById('doc-selected');
            sel.style.display = 'flex';
            document.getElementById('doc-filename').textContent = name;
            document.getElementById('doc-dropzone').style.borderColor = 'var(--gold)';
            document.getElementById('doc-dropzone').style.background = 'rgba(196,144,16,0.04)';
        }
    </script>

</x-app-layout>