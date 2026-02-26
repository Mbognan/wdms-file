@extends('admin.layouts.master')

@section('contents')
    <style>
        /* ================= ASSIGNED USERS ================= */
        .assigned-users {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 12px;
        }

        .assigned-users .avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            border: 2px solid #fff;
            background: #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            color: #1e40af;
            font-size: 13px;
            margin-left: -10px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, .15);
        }

        .assigned-users .avatar:first-child {
            margin-left: 0;
        }

        .assigned-users .more {
            background: #2563eb;
            color: #fff;
        }

        /* ================= TAG INPUT ================= */
        .tag-input {
            border: 1px solid #d1d5db;
            border-radius: 6px;
            padding: 8px;
        }

        .tag-input .tags {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-bottom: 6px;
        }

        .tag-input input {
            border: none;
            outline: none;
            width: 100%;
        }
    </style>

    <div class="container-xxl container-p-y">

        {{-- HEADER WITH BACK BUTTON --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4>
                <a href="{{ route('admin.accreditation.index') }}">
                    <span class="text-muted fw-light">Accreditation</span>
                </a>
                / Area Details
            </h4>
            <a href="{{ url()->previous() }}" class="btn btn-secondary btn-sm">
                <i class="bx bx-arrow-back me-1"></i> Back
            </a>
        </div>

        {{-- ================= PROGRAM CARD ================= --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0 fw-bold">{{ $programName }}</h5>
                    <small class="text-muted">Level: {{ $level }}</small>
                </div>
                @if ($isAdmin)
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#assignAreasModal">
                        <i class="bx bx-map-pin me-1"></i> Add Area
                    </button>
                @endif
            </div>

            <div class="card-body">
                <p class="text-muted mb-4 text-center">
                    This section lists the Areas under this program.
                </p>


                <div class="row g-3">
                    @foreach ($programAreas as $area)
                        <div class="col-md-4">
                            <div class="card h-100 shadow-sm d-flex flex-column area-card"
                                data-area-id="{{ $area->id }}">

                                {{-- CLICKABLE AREA --}}
                                <a href="{{ route('program.areas.parameters', [$infoId, $levelId, $programId, $area->id]) }}"
                                class="text-decoration-none text-dark flex-grow-1 area-link">

                                    <div class="bg-primary text-white text-center py-2 rounded-top fw-bold">
                                        {{ $area->area_name }}
                                    </div>

                                    <div class="card-body text-center">
                                        <div class="assigned-users mb-2">
                                            @foreach ($area->users->take(3) as $user)
                                                <div class="avatar">{{ substr($user->name, 0, 2) }}</div>
                                            @endforeach
                                            @if ($area->users->count() > 3)
                                                <div class="avatar more">+{{ $area->users->count() - 3 }}</div>
                                            @endif
                                        </div>

                                        <p class="text-muted">{{ $area->description ?? '' }}</p>
                                    </div>
                                </a>

                                @if ($isAdmin || $isDean)
                                    <div class="p-2 border-top text-center">
                                        <button
                                            type="button"
                                            class="btn btn-outline-primary btn-sm assign-user-btn"
                                            data-area-id="{{ $area->id }}"
                                            data-area-name="{{ $area->area_name }}"
                                            data-bs-toggle="modal"
                                            data-bs-target="#assignUsersModal">

                                            <i class="bx bx-user-plus"></i>

                                            {{ $isAdmin ? 'Assign Internal Assessors' : 'Assign Task Forces' }}

                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- ================= ASSIGN AREAS & USERS MODAL ================= --}}
    <div class="modal fade" id="assignAreasModal" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">
                        {{ 
                            $isAdmin
                            ? 'Add Area & Assign Internal Assessors'
                            : 'Assign Task Forces'
                        }}
                    </h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    {{-- ================= FORM ================= --}}
                    <form id="areasForm" method="POST" action="{{ route('programs.areas.save', $programId) }}">
                        @csrf

                        {{-- ✅ REQUIRED CONTEXT (FIX) --}}
                        <input type="hidden" name="level_id" value="{{ $levelId }}">
                        <input type="hidden" name="accreditation_info_id" value="{{ $infoId }}">

                        <div class="table-responsive">
                            <table class="table table-bordered align-middle text-center">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width:30%">Area Name</th>
                                        <th style="width:55%">
                                            {{ $isAdmin ? 'Internal Assessors' : 'Task Forces' }}
                                        </th>
                                        <th style="width:15%">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="areaTableBody">
                                    {{-- JS Injects Rows --}}
                                </tbody>
                            </table>
                        </div>
                        @if ($isAdmin)
                            <button type="button" class="btn btn-outline-primary btn-sm" id="addAreaRow">
                                <i class="bx bx-plus"></i> Add Area
                            </button>
                        @endif

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        Save Assignments
                    </button>
                </div>
                </form>

            </div>
        </div>
    </div>
{{-- ================= ASSIGN USERS MODAL ================= --}}
<div class="modal fade" id="assignUsersModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">
                    Assign {{ 
                    $isAdmin
                    ? 'Internal Assessors' 
                    : 'Task Forces' }} to <span id="assignAreaName"></span>
                </h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="assignUsersForm" method="POST" action="{{ route('areas.assign.users') }}">
                @csrf

                {{-- CONTEXT --}}
                <input type="hidden" name="area_id" id="assignAreaId">
                <input type="hidden" name="program_id" value="{{ $programId }}">
                <input type="hidden" name="level_id" value="{{ $levelId }}">
                <input type="hidden" name="accreditation_info_id" value="{{ $infoId }}">

                <div class="modal-body">
                    <label class="fw-bold mb-2">
                        {{ $isAdmin ? 'Select Internal Assessors' : 'Select Task Forces' }}
                    </label>
                    @if ($isDean)
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th style="width:50%">Task Forces</th>
                                    <th style="width:30%">Role</th>
                                    <th style="width:20%">Action</th>
                                </tr>
                            </thead>
                            <tbody id="taskForceTable"></tbody>
                        </table>

                        <button type="button" class="btn btn-sm btn-outline-primary" id="addTaskForceRow">
                            + Add Task Force
                        </button>
                    @elseif ($isAdmin)
                        <select
                            class="form-control js-assign-users"
                            name="users[]"
                            multiple
                            style="width:100%">
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    @endif
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        Assign
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>
@push('scripts')
<script>
$(function () {

    /* =====================================================
       GLOBAL STATE
    ====================================================== */

    const programId = '{{ $programId }}';

    const ALL_USERS = [
        @foreach ($users as $user)
            { id: "{{ $user->id }}", name: "{{ $user->name }}" },
        @endforeach
    ];

    let taskForceIndex = 0;
    let areas = [];
    let areaIndex = 0;


    /* =====================================================
       MODAL OPEN HANDLER
    ====================================================== */

    $(document).on('click', '.assign-user-btn', function () {

        const areaId = $(this).data('area-id');
        $('#assignAreaId').val(areaId);
        $('#assignAreaName').text($(this).data('area-name'));

        // Clear old rows
        $('#taskForceTable').empty();
        taskForceIndex = 0;

        // Fetch already assigned users
        $.get(`/admin/areas/${areaId}/assigned-users`)
            .done(res => {

                window.ASSIGNED_USERS = res.users.map(u => String(u.id));

                if ($('.js-assign-users').length) {

                    const select = $('.js-assign-users');

                    select.val(window.ASSIGNED_USERS).trigger('change');
                }
            })

            .fail(() => {
                window.ASSIGNED_USERS = [];
            });
    });

    $('.js-assign-users').select2({
        dropdownParent: $('#assignUsersModal'),
        width: '100%',
        placeholder: "Select users...",
        allowClear: true,
        closeOnSelect: false,
        templateResult: formatUser,
        templateSelection: formatUserSelection
    });

    /* =====================================================
       TASK FORCE SECTION
    ====================================================== */

    function initSelect2(context) {
        context.find('.select-user').select2({
            dropdownParent: $('#assignUsersModal'),
            width: '100%'
        });
    }

    function getSelectedUsers() {
        return $('.select-user')
            .map(function () { return $(this).val(); })
            .get()
            .filter(Boolean);
    }

    function updateUserDropdowns() {

        const selectedUsers = getSelectedUsers();
        const assignedUsers = window.ASSIGNED_USERS || [];

        $('.select-user').each(function () {

            const currentValue = $(this).val();
            const select = $(this);

            select.empty().append(
                `<option value="" disabled>Select Task Force</option>`
            );

            ALL_USERS.forEach(user => {

                if (
                    (!selectedUsers.includes(user.id) || user.id === currentValue) &&
                    (!assignedUsers.includes(user.id) || user.id === currentValue)
                ) {
                    select.append(
                        `<option value="${user.id}">${user.name}</option>`
                    );
                }
            });

            select.val(currentValue).trigger('change.select2');
        });
    }

    function updateChairAvailability() {

        const chairExists = $('.role-select')
            .toArray()
            .some(el => $(el).val() === 'chair');

        $('.role-select').each(function () {
            const isCurrentChair = $(this).val() === 'chair';
            $(this)
                .find('option[value="chair"]')
                .prop('disabled', chairExists && !isCurrentChair);
        });
    }

    function validateTaskForceForm() {

        let users = [];
        let chairCount = 0;

        for (const row of $('#taskForceTable tr')) {

            const user = $(row).find('.select-user').val();
            const role = $(row).find('.role-select').val();

            if (!user || !role) {
                return 'All fields are required.';
            }

            if (users.includes(user)) {
                return 'A user can only be assigned once.';
            }

            users.push(user);

            if (role === 'chair') chairCount++;
        }

        if (chairCount > 1) {
            return 'Only one Chair is allowed per area.';
        }

        return null;
    }

    $('#addTaskForceRow').on('click', function () {

        const row = $(`
            <tr>
                <td>
                    <select name="users[${taskForceIndex}][id]"
                            class="form-select form-select-sm select-user"
                            required>
                    </select>
                </td>
                <td>
                    <select name="users[${taskForceIndex}][role]"
                            class="form-select form-select-sm role-select"
                            required>
                        <option value="chair">Chair</option>
                        <option value="member" selected>Member</option>
                    </select>
                </td>
                <td class="text-center">
                    <button type="button"
                            class="btn btn-sm btn-outline-danger remove-row">
                        <i class="bx bx-trash"></i>
                    </button>
                </td>
            </tr>
        `);

        $('#taskForceTable').append(row);

        initSelect2(row);

        taskForceIndex++;

        updateUserDropdowns();
        updateChairAvailability();
    });

    $(document).on('change', '.select-user', updateUserDropdowns);
    $(document).on('change', '.role-select', updateChairAvailability);

    $(document).on('click', '.remove-row', function () {
        $(this).closest('tr').remove();
        updateUserDropdowns();
        updateChairAvailability();
    });

    $('#assignUsersForm').on('submit', function (e) {

        e.preventDefault();

        const error = validateTaskForceForm();

        if (error) {
            showToast(error, 'error');
            return;
        }

        $.post($(this).attr('action'), $(this).serialize())
            .done(res => {
                showToast(res.message, 'success');

                // Close modal
                $('#assignUsersModal').modal('hide');

               refreshProgramAreas();
            })

            .fail(xhr => {
                showToast(
                    xhr.responseJSON?.message || 'Failed to assign users',
                    'error'
                );
            });
    });


    /* =====================================================
       AREA SECTION
    ====================================================== */

    $('#addAreaRow').on('click', function () {

        const id = ++areaIndex;

        areas.push({ id, name: '', users: [] });

        const row = $(`
            <tr data-id="${id}">
                <td>
                    <input type="text"
                           class="form-control area-name"
                           placeholder="Area name"
                           data-id="${id}">
                </td>
                <td>
                    <select class="form-select user-select"
                            multiple data-id="${id}">
                        ${ALL_USERS.map(u =>
                            `<option value="${u.id}">${u.name}</option>`
                        ).join('')}
                    </select>
                </td>
                <td>
                    <button type="button"
                            class="btn btn-sm btn-outline-danger btn-remove"
                            data-id="${id}">
                        Delete
                    </button>
                </td>
            </tr>
        `);

        $('#areaTableBody').append(row);

        row.find('.user-select').select2({
            dropdownParent: $('#assignAreasModal'),
            width: '100%'
        });
    });

    $(document).on('change', '.user-select', function () {
        const id = Number($(this).data('id'));
        const area = areas.find(a => a.id === id);
        if (area) area.users = ($(this).val() || []).map(Number);
    });

    $(document).on('input', '.area-name', function () {
        const id = Number($(this).data('id'));
        const area = areas.find(a => a.id === id);
        if (area) area.name = this.value;
    });

    $(document).on('click', '.btn-remove', function () {
        const id = Number($(this).data('id'));
        areas = areas.filter(a => a.id !== id);
        $(this).closest('tr').remove();
    });

    $('#areasForm').on('submit', function (e) {

        e.preventDefault();

        $(this).find('input[name^="areas"]').remove();

        areas.forEach((area, i) => {

            $(this).append(
                `<input type="hidden" name="areas[${i}][name]" value="${area.name}">`
            );

            area.users.forEach(uid => {
                $(this).append(
                    `<input type="hidden" name="areas[${i}][users][]" value="${uid}">`
                );
            });
        });

        $.post($(this).attr('action'), $(this).serialize())
            .done(res => {
                showToast(res.message, 'success');
                areas = [];
                $('#areaTableBody').empty();
                $('#assignAreasModal').modal('hide');
                refreshProgramAreas();
            })
            .fail(xhr => {
                showToast(
                    xhr.responseJSON?.message || 'Something went wrong!',
                    'error'
                );
            });
    });


    /* =====================================================
       REFRESH PROGRAM AREAS
    ====================================================== */

    function refreshProgramAreas() {

        $.get(`/admin/programs/${programId}/areas`)
            .done(data => {

                const container = $('.row.g-3').empty();

                data.forEach(area => {

                    const usersHtml = area.users.slice(0, 3)
                        .map(u => `
                            <div class="avatar">
                                ${getInitials(u.name)}
                            </div>
                        `).join('');

                    const more = area.users.length > 3
                        ? `<div class="avatar more">
                            +${area.users.length - 3}
                        </div>`
                        : '';

                    container.append(`
                        <div class="col-md-4">
                            <div class="card h-100 shadow-sm d-flex flex-column area-card"
                                data-area-id="${area.id}">

                                <a href="/admin/programs/${programId}/areas/${area.id}/parameters"
                                class="text-decoration-none text-dark flex-grow-1">

                                    <div class="bg-primary text-white text-center py-2 rounded-top fw-bold">
                                        ${area.name}
                                    </div>

                                    <div class="card-body text-center">
                                        <div class="assigned-users">
                                            ${usersHtml + more}
                                        </div>
                                    </div>

                                </a>
                            </div>
                        </div>
                    `);
                });
            });
    }

    function getInitials(name) {
        if (!name) return '';

        const parts = name.trim().split(' ');
        if (parts.length === 1) {
            return parts[0].substring(0, 2).toUpperCase();
        }

        return (parts[0][0] + parts[1][0]).toUpperCase();
    }

    function formatUser(user) {

        if (!user.id) return user.text;

        const initials = getInitials(user.text);

        return $(`
            <div style="display:flex;align-items:center;gap:8px;">
                <div style="
                    width:28px;
                    height:28px;
                    border-radius:50%;
                    background:#2563eb;
                    color:#fff;
                    font-size:11px;
                    font-weight:600;
                    display:flex;
                    align-items:center;
                    justify-content:center;">
                    ${initials}
                </div>
                <span>${user.text}</span>
            </div>
        `);
    }

    function formatUserSelection(user) {

        if (!user.id) return user.text;

        const initials = getInitials(user.text);

        return $(`
            <span style="
                background:#e5e7eb;
                padding:4px 8px;
                border-radius:20px;
                font-size:12px;
                display:flex;
                align-items:center;
                gap:6px;">
                <span style="
                    width:20px;
                    height:20px;
                    border-radius:50%;
                    background:#2563eb;
                    color:#fff;
                    font-size:10px;
                    font-weight:600;
                    display:flex;
                    align-items:center;
                    justify-content:center;">
                    ${initials}
                </span>
                ${user.text}
            </span>
        `);
    }


    function updateAreaAvatars(areaId, users) {

        const card = $(`.area-card[data-area-id="${areaId}"]`);

        if (!card.length) return;

        const container = card.find('.assigned-users');

        container.empty();

        if (!users || !users.length) return;

        // Remove duplicates safely
        const uniqueUsers = [];
        const seen = new Set();

        users.forEach(u => {
            if (!seen.has(u.id)) {
                seen.add(u.id);
                uniqueUsers.push(u);
            }
        });

        const firstThree = uniqueUsers.slice(0, 3);

        firstThree.forEach(user => {
            container.append(`
                <div class="avatar">
                    ${getInitials(user.name)}
                </div>
            `);
        });

        if (uniqueUsers.length > 3) {
            container.append(`
                <div class="avatar more">
                    +${uniqueUsers.length - 3}
                </div>
            `);
        }
    }
});
</script>
@endpush

@endsection