<x-app-layout>
    <x-slot name="header">Donation History</x-slot>

    @php $user = auth()->user(); @endphp

    <div style="margin-bottom:40px;">
        <div class="section-label">Records</div>
        <h2 style="font-family:'Cormorant Garamond',serif;font-size:38px;font-weight:700;color:var(--dark);line-height:1.1;margin-bottom:12px;">
            @if($user->role === 'admin') All Donation History
            @elseif($user->role === 'donor') Your Donation History
            @else Donations You've Received
            @endif
        </h2>
        <p style="font-size:15px;color:var(--muted);max-width:560px;line-height:1.8;">
            @if($user->role === 'admin') Complete record of all donations made by donors to charity organizations.
            @elseif($user->role === 'donor') A record of all food donations you have posted on the platform.
            @else A record of all donations your organization has claimed and received.
            @endif
        </p>
    </div>

    @if(session('success'))
        <div style="background:rgba(26,122,64,0.08);border:1px solid rgba(26,122,64,0.2);color:#1A7A40;padding:14px 20px;border-radius:10px;margin-bottom:24px;font-size:14px;font-weight:600;">
            {{ session('success') }}
        </div>
    @endif

    <form id="bulk-form" method="POST" action="{{ route('donations.bulk.delete') }}">
        @csrf
        @method('DELETE')

        <div class="card" style="overflow:hidden;">
            @if($donations->isEmpty())
                <div style="padding:80px 40px;text-align:center;">
                    <div style="width:64px;height:64px;background:rgba(196,144,16,0.1);border-radius:16px;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
                        <svg width="28" height="28" fill="none" stroke="var(--gold)" stroke-width="1.5" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                    </div>
                    <h3 style="font-family:'Cormorant Garamond',serif;font-size:24px;font-weight:700;color:var(--dark);margin-bottom:8px;">No records yet</h3>
                    <p style="font-size:14px;color:var(--muted);margin-bottom:24px;">
                        @if($user->role === 'donor') You haven't posted any donations yet.
                        @elseif($user->role === 'charity') You haven't received any donations yet.
                        @else No donations have been made on the platform yet.
                        @endif
                    </p>
                    @if($user->role === 'donor')
                        <a href="{{ route('donations.create') }}" class="btn btn-red">Post Your First Donation</a>
                    @elseif($user->role === 'charity')
                        <a href="{{ route('donations.available') }}" class="btn btn-gold">Browse Available Food</a>
                    @endif
                </div>
            @else
                <div style="display:flex;align-items:center;gap:16px;padding:14px 20px;border-bottom:1px solid rgba(196,144,16,0.1);background:var(--lighter);">
                    <label style="display:flex;align-items:center;gap:8px;font-size:13px;font-weight:600;color:var(--muted);cursor:pointer;user-select:none;">
                        <input type="checkbox" id="select-all" style="width:15px;height:15px;accent-color:var(--red);cursor:pointer;">
                        Select All
                    </label>
                    <button type="button" id="delete-btn"
                            onclick="openDeleteModal()"
                            class="btn btn-red"
                            style="padding:8px 18px;font-size:12px;display:none;">
                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4a1 1 0 011-1h4a1 1 0 011 1v2"/></svg>
                        Delete Selected (<span id="selected-count">0</span>)
                    </button>
                </div>

                <div style="overflow-x:auto;">
                    <table class="ps-table">
                        <thead>
                            <tr>
                                <th style="width:40px;"></th>
                                <th>Food Description</th>
                                <th>Target Audience</th>
                                <th>Affected Area</th>
                                @if($user->role === 'admin' || $user->role === 'donor')
                                    <th>Directed To</th>
                                    <th>Claimed By</th>
                                @endif
                                @if($user->role === 'charity')
                                    <th>Donor</th>
                                @endif
                                <th>Status</th>
                                <th>Date Posted</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($donations as $donation)
                            @php
                                $displayStatus = in_array($donation->status, ['claimed', 'completed']) ? 'completed' : $donation->status;
                            @endphp
                            <tr>
                                <td style="text-align:center;">
                                    <input type="checkbox" name="ids[]" value="{{ $donation->id }}"
                                           class="row-checkbox"
                                           style="width:15px;height:15px;accent-color:var(--red);cursor:pointer;">
                                </td>
                                <td style="font-weight:600;color:var(--dark);max-width:260px;">
                                    <div style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:260px;" title="{{ $donation->description }}">
                                        {{ $donation->description }}
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-muted">{{ $donation->target_audience }}</span>
                                </td>
                                <td style="color:var(--red);font-size:13px;">
                                    @if($donation->affected_area)
                                        <span style="display:inline-flex;align-items:center;gap:4px;">
                                            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                            {{ $donation->affected_area }}
                                        </span>
                                    @else
                                        -
                                    @endif
                                </td>
                                @if($user->role === 'admin' || $user->role === 'donor')
                                    <td style="color:var(--muted);font-size:13px;">{{ $donation->charity?->name ?? '-' }}</td>
                                    <td style="font-size:13px;">{{ $donation->claimedBy?->name ?? '-' }}</td>
                                @endif
                                @if($user->role === 'charity')
                                    <td style="font-size:13px;">
                                        <div style="font-weight:600;color:var(--dark);">{{ $donation->donor?->name ?? '-' }}</div>
                                    </td>
                                @endif
                                <td>
                                    <span class="badge {{
                                        $displayStatus === 'available'  ? 'badge-green' :
                                        ($displayStatus === 'completed' ? 'badge-blue'  : 'badge-muted')
                                    }}">{{ ucfirst($displayStatus) }}</span>
                                </td>
                                <td style="color:var(--muted);font-size:13px;white-space:nowrap;">
                                    {{ $donation->created_at->format('M d, Y') }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </form>

    {{-- ═══ DELETE CONFIRMATION MODAL ═══ --}}
    <div id="delete-modal" style="display:none;position:fixed;inset:0;z-index:9999;align-items:center;justify-content:center;">
        <div onclick="closeDeleteModal()" style="position:absolute;inset:0;background:rgba(22,8,0,0.55);backdrop-filter:blur(4px);"></div>
        <div id="delete-modal-box" style="position:relative;background:var(--white);border-radius:24px;max-width:420px;width:90%;box-shadow:0 32px 80px rgba(22,8,0,0.25);overflow:hidden;transform:translateY(20px);opacity:0;transition:all 0.3s cubic-bezier(0.34,1.56,0.64,1);">

            {{-- Header --}}
            <div style="background:linear-gradient(135deg,var(--dark),#2A0E04);padding:28px 32px 24px;position:relative;overflow:hidden;">
                <div style="position:absolute;top:0;left:0;bottom:0;width:3px;background:linear-gradient(180deg,var(--red),var(--gold));"></div>
                <div style="position:absolute;top:-40px;right:-40px;width:120px;height:120px;border-radius:50%;background:rgba(176,40,24,0.1);"></div>
                <div style="position:relative;">
                    <div style="width:48px;height:48px;background:linear-gradient(135deg,var(--red),var(--red2));border-radius:13px;display:flex;align-items:center;justify-content:center;margin-bottom:14px;box-shadow:0 6px 20px rgba(176,40,24,0.4);">
                        <svg width="22" height="22" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4a1 1 0 011-1h4a1 1 0 011 1v2"/></svg>
                    </div>
                    <h3 style="font-family:'Cormorant Garamond',serif;font-size:24px;font-weight:700;color:white;margin-bottom:4px;">Delete Records</h3>
                    <p style="font-size:13px;color:rgba(255,255,255,0.4);">This action cannot be undone</p>
                </div>
            </div>

            {{-- Body --}}
            <div style="padding:28px 32px;">
                <div style="background:rgba(176,40,24,0.05);border:1px solid rgba(176,40,24,0.15);border-radius:12px;padding:16px 18px;margin-bottom:20px;display:flex;align-items:flex-start;gap:12px;">
                    <svg width="18" height="18" fill="none" stroke="var(--red)" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0;margin-top:1px;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <div>
                        <div style="font-size:13px;font-weight:700;color:var(--red);margin-bottom:4px;">
                            Deleting <span id="modal-delete-count">0</span> record(s)
                        </div>
                        <div style="font-size:12px;color:var(--muted);line-height:1.6;">
                            This will permanently remove the selected donations along with all related claims, feedback, and notifications.
                        </div>
                    </div>
                </div>

                <p style="font-size:13px;color:var(--muted);line-height:1.7;margin-bottom:24px;">
                    Are you sure you want to proceed? This data cannot be recovered once deleted.
                </p>

                <div style="display:flex;gap:12px;">
                    <button onclick="closeDeleteModal()" class="btn btn-ghost" style="flex:1;padding:13px;justify-content:center;">
                        Cancel
                    </button>
                    <button onclick="confirmDelete()" class="btn btn-red" style="flex:2;padding:13px;justify-content:center;" id="modal-delete-confirm-btn">
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/></svg>
                        Yes, Delete
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        const selectAll  = document.getElementById('select-all');
        const deleteBtn  = document.getElementById('delete-btn');
        const countSpan  = document.getElementById('selected-count');
        const checkboxes = () => document.querySelectorAll('.row-checkbox');

        function updateDeleteBtn() {
            const checked = document.querySelectorAll('.row-checkbox:checked').length;
            if (countSpan) countSpan.textContent = checked;
            if (deleteBtn) deleteBtn.style.display = checked > 0 ? 'inline-flex' : 'none';
            if (selectAll) {
                selectAll.indeterminate = checked > 0 && checked < checkboxes().length;
                selectAll.checked = checkboxes().length > 0 && checked === checkboxes().length;
            }
        }

        if (selectAll) {
            selectAll.addEventListener('change', function () {
                checkboxes().forEach(cb => cb.checked = this.checked);
                updateDeleteBtn();
            });
        }

        document.addEventListener('change', function (e) {
            if (e.target.classList.contains('row-checkbox')) updateDeleteBtn();
        });

        function openDeleteModal() {
            const checked = document.querySelectorAll('.row-checkbox:checked').length;
            if (checked === 0) return;
            document.getElementById('modal-delete-count').textContent = checked;
            const modal = document.getElementById('delete-modal');
            modal.style.display = 'flex';
            setTimeout(() => {
                const box = document.getElementById('delete-modal-box');
                box.style.transform = 'translateY(0)';
                box.style.opacity = '1';
            }, 10);
        }

        function closeDeleteModal() {
            const box = document.getElementById('delete-modal-box');
            box.style.transform = 'translateY(20px)';
            box.style.opacity = '0';
            setTimeout(() => {
                document.getElementById('delete-modal').style.display = 'none';
            }, 250);
        }

        function confirmDelete() {
            const btn = document.getElementById('modal-delete-confirm-btn');
            btn.disabled = true;
            btn.innerHTML = 'Deleting...';
            document.getElementById('bulk-form').submit();
        }

        document.addEventListener('keydown', e => { if (e.key === 'Escape') closeDeleteModal(); });
    </script>

</x-app-layout>