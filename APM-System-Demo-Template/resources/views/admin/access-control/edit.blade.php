@extends('layouts.employeehub')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h4 class="mb-1">Access Control</h4>
            <div class="text-muted small">Manage access for {{ $managedUser->name }}.</div>
        </div>
        <a class="btn btn-outline-secondary" href="{{ route('admin.access-control.index') }}">Back</a>
    </div>

    @if(session('status') === 'access-updated')
        <div class="alert alert-success">Access settings updated.</div>
    @endif

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.access-control.update', $managedUser) }}">
                @csrf
                @method('PUT')

                <div class="d-flex flex-wrap gap-2 mb-4">
                    <button type="button" class="btn btn-sm btn-outline-success" id="check-all-permissions">Check All</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="uncheck-all-permissions">Uncheck All</button>
                </div>

                <div class="border rounded p-3 mb-4 bg-light-subtle">
                    <div class="fw-semibold mb-2">Quick Sidebar Access</div>
                    <div class="text-muted small mb-3">Check a sidebar section below to automatically select all permissions used by that section.</div>
                    <div class="row g-3">
                        @foreach($permissionGroups as $groupKey => $group)
                            @php
                                $allChecked = collect($group['permissions'])->every(fn ($permission) => in_array($permission, $selected, true));
                            @endphp
                            <div class="col-md-4">
                                <div class="form-check border rounded p-3 h-100 bg-white">
                                    <input
                                        class="form-check-input permission-group-toggle"
                                        type="checkbox"
                                        id="group_toggle_{{ $groupKey }}"
                                        data-group="{{ $groupKey }}"
                                        @checked($allChecked)
                                    >
                                    <label class="form-check-label fw-semibold" for="group_toggle_{{ $groupKey }}">
                                        {{ $group['label'] }}
                                    </label>
                                    <div class="text-muted small mt-2">{{ $group['description'] }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="row g-3">
                    @foreach($permissionGroups as $groupKey => $group)
                        <div class="col-12">
                            <div class="border rounded p-3">
                                <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
                                    <div>
                                        <div class="fw-semibold">{{ $group['label'] }}</div>
                                        <div class="text-muted small">{{ $group['description'] }}</div>
                                        @if(!empty($group['notes']))
                                            <div class="mt-2">
                                                @foreach($group['notes'] as $note)
                                                    <div class="small text-muted">{{ $note }}</div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                    <div class="form-check">
                                        <input
                                            class="form-check-input permission-group-toggle"
                                            type="checkbox"
                                            id="group_card_toggle_{{ $groupKey }}"
                                            data-group="{{ $groupKey }}"
                                            @checked(collect($group['permissions'])->every(fn ($permission) => in_array($permission, $selected, true)))
                                        >
                                        <label class="form-check-label small fw-semibold" for="group_card_toggle_{{ $groupKey }}">
                                            Select {{ $group['label'] }}
                                        </label>
                                    </div>
                                </div>

                                <div class="row g-3">
                                    @foreach($group['permissions'] as $permissionKey)
                                        <div class="col-md-4">
                                            <div class="form-check">
                                                <input
                                                    class="form-check-input access-permission-checkbox"
                                                    type="checkbox"
                                                    name="access_permissions[]"
                                                    value="{{ $permissionKey }}"
                                                    id="perm_{{ $permissionKey }}"
                                                    data-group="{{ $groupKey }}"
                                                    @checked(in_array($permissionKey, $selected, true))
                                                >
                                                <label class="form-check-label" for="perm_{{ $permissionKey }}">
                                                    {{ $permissions[$permissionKey] ?? $permissionKey }}
                                                </label>
                                            </div>
                                            @if(!empty($group['includes'][$permissionKey]))
                                                <div class="small text-muted ms-4 mt-1">
                                                    Includes: {{ implode(', ', $group['includes'][$permissionKey]) }}
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-4 d-flex justify-content-end">
                    <button class="btn btn-primary">Save Access</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        (() => {
            const checkAllBtn = document.getElementById('check-all-permissions');
            const uncheckAllBtn = document.getElementById('uncheck-all-permissions');
            const checkboxes = Array.from(document.querySelectorAll('.access-permission-checkbox'));
            const groupToggles = Array.from(document.querySelectorAll('.permission-group-toggle'));

            const syncGroupState = (groupName) => {
                const groupCheckboxes = checkboxes.filter((checkbox) => checkbox.dataset.group === groupName);
                const groupToggleTargets = groupToggles.filter((toggle) => toggle.dataset.group === groupName);
                const allChecked = groupCheckboxes.length > 0 && groupCheckboxes.every((checkbox) => checkbox.checked);

                groupToggleTargets.forEach((toggle) => {
                    toggle.checked = allChecked;
                });
            };

            const syncAllGroups = () => {
                const groups = [...new Set(checkboxes.map((checkbox) => checkbox.dataset.group))];
                groups.forEach(syncGroupState);
            };

            groupToggles.forEach((toggle) => {
                toggle.addEventListener('change', () => {
                    const groupName = toggle.dataset.group;
                    const groupCheckboxes = checkboxes.filter((checkbox) => checkbox.dataset.group === groupName);

                    groupCheckboxes.forEach((checkbox) => {
                        checkbox.checked = toggle.checked;
                    });

                    groupToggles
                        .filter((otherToggle) => otherToggle.dataset.group === groupName)
                        .forEach((otherToggle) => {
                            otherToggle.checked = toggle.checked;
                        });
                });
            });

            checkboxes.forEach((checkbox) => {
                checkbox.addEventListener('change', () => {
                    syncGroupState(checkbox.dataset.group);
                });
            });

            if (checkAllBtn) {
                checkAllBtn.addEventListener('click', () => {
                    checkboxes.forEach((checkbox) => {
                        checkbox.checked = true;
                    });
                    syncAllGroups();
                });
            }

            if (uncheckAllBtn) {
                uncheckAllBtn.addEventListener('click', () => {
                    checkboxes.forEach((checkbox) => {
                        checkbox.checked = false;
                    });
                    syncAllGroups();
                });
            }

            syncAllGroups();
        })();
    </script>
@endsection
