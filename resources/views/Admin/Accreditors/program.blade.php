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

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4>
                <span class="text-muted fw-light">Admin / Accreditation /</span> Area Details
            </h4>
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
                            <div class="card h-100 shadow-sm d-flex flex-column area-card">

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

    {{-- ✅ BUTTON OUTSIDE THE LINK --}}
    @if ($isAdmin)
        <div class="p-2 border-top text-center">
            <button
                type="button"
                class="btn btn-outline-primary btn-sm assign-user-btn"
                data-area-id="{{ $area->id }}"
                data-area-name="{{ $area->area_name }}"
                data-bs-toggle="modal"
                data-bs-target="#assignUsersModal">
                <i class="bx bx-user-plus"></i> Assign User
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
                    <h5 class="modal-title">Assign Areas & Users</h5>
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
                                        <th style="width:55%">Assign Users</th>
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
                    Assign Users to <span id="assignAreaName"></span>
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
                    <label class="fw-bold mb-2">Select Users</label>
                    <select
                        class="form-control js-assign-users"
                        name="users[]"
                        multiple
                        style="width:100%">
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        Save Users
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>

    @push('scripts')
    <script>
    /* ================= OPEN ASSIGN USER MODAL ================= */
    $(document).on('click', '.assign-user-btn', function () {
        const areaId = $(this).data('area-id');
        const areaName = $(this).data('area-name');

        $('#assignAreaId').val(areaId);
        $('#assignAreaName').text(areaName);

        $('.js-assign-users').val(null).trigger('change');
    });

    /* ================= SELECT2 ================= */
    $('.js-assign-users').select2({
        dropdownParent: $('#assignUsersModal'),
        width: '100%'
    });

    /* ================= SUBMIT ================= */
   $('#assignUsersForm').on('submit', function (e) {
    e.preventDefault();
    e.stopPropagation(); // 🔐 VERY IMPORTANT

    const url = $(this).attr('action');
    const data = $(this).serialize();

    $.ajax({
        url: url,
        type: 'POST',
        data: data,
        success: function (res) {
            showToast(res.message, 'success');
            $('#assignUsersModal').modal('hide');
            refreshProgramAreas();
        },
        error: function (xhr) {
            showToast(
                xhr.responseJSON?.message || 'Failed to assign users',
                'error'
            );
        }
    });
});

</script>

        <script>
            const ASSIGNED_USERS = @json($assignedUserIds);

            const USERS = [
                @foreach ($users as $user)
                    @if (!$assignedUserIds->contains($user->id))
                        {
                            id: {{ $user->id }},
                            name: "{{ $user->name }}"
                        },
                    @endif
                @endforeach
            ];



            /* ================= STATE ================= */
            let areas = [];
            let areaId = 0;

            /* ================= ADD AREA ================= */
            document.getElementById('addAreaRow').addEventListener('click', () => {
                const id = ++areaId;

                areas.push({
                    id,
                    name: '',
                    users: []
                });

                document.getElementById('areaTableBody').insertAdjacentHTML('beforeend', `
        <tr data-id="${id}">
            <td>
                <input type="text"
                       class="form-control area-name"
                       placeholder="Area name"
                       data-id="${id}">
            </td>
            <td>
               <select class="js-example-basic-multiple user-select" multiple data-id="${id}">
    ${USERS.map(u =>
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

                $('.js-example-basic-multiple').select2({
                    width: '100%',
                    dropdownParent: $('#assignAreasModal')
                });
            });

            function refreshProgramAreas() {
                const programId = '{{ $programId }}';

                $.get('/admin/programs/' + programId + '/areas')
                    .done(function(data) {
                        const container = $('.row.g-3');
                        container.empty();

                        data.forEach(area => {
                            let usersHtml = '';
                            area.users.forEach((u, i) => {
                                if (i < 3) usersHtml +=
                                    `<div class="avatar">${u.name.substring(0,2)}</div>`;
                            });
                            if (area.users.length > 3) usersHtml +=
                                `<div class="avatar more">+${area.users.length - 3}</div>`;

                            const cardHtml = `
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm d-flex flex-column">
                        <div class="bg-primary text-white text-center py-2 rounded-top fw-bold">${area.name}</div>
                        <div class="card-body text-center">
                            <div class="assigned-users">${usersHtml}</div>
                        </div>
                    </div>
                </div>`;
                            container.append(cardHtml);
                        });
                    });
            }

            /* ================= UPDATE USERS ================= */
            $(document).on('change', '.user-select', function() {
                const id = Number($(this).data('id'));
                const area = areas.find(a => a.id === id);
                if (area) area.users = ($(this).val() || []).map(Number);
            });

            /* ================= UPDATE NAME ================= */
            $(document).on('input', '.area-name', function() {
                const id = Number($(this).data('id'));
                const area = areas.find(a => a.id === id);
                if (area) area.name = this.value;
            });

            /* ================= REMOVE ================= */
            $(document).on('click', '.btn-remove', function() {
                const id = Number($(this).data('id'));
                areas = areas.filter(a => a.id !== id);
                $(this).closest('tr').remove();
            });

            $('#areasForm').on('submit', function(e) {
                e.preventDefault();

                this.querySelectorAll('input[name^="areas"]').forEach(el => el.remove());

                areas.forEach((area, i) => {
                    this.insertAdjacentHTML('beforeend',
                        `<input type="hidden" name="areas[${i}][name]" value="${area.name}">`
                    );

                    area.users.forEach(uid => {
                        this.insertAdjacentHTML('beforeend',
                            `<input type="hidden" name="areas[${i}][users][]" value="${uid}">`
                        );
                    });
                });

                const url = $(this).attr('action');
                const data = $(this).serialize();

                $.post(url, data)
                    .done(function(response) {
                        showToast(response.message, 'success');
                        areas = [];
                        $('#areaTableBody').empty();
                        $('#assignAreasModal').modal('hide');
                        refreshProgramAreas();
                    })
                    .fail(function(xhr) {
                        const msg = xhr.responseJSON?.message || 'Something went wrong!';
                        showToast(msg, 'error');
                    });
            });
        </script>
    @endpush
@endsection
