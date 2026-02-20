@extends('admin.layouts.master')

@section('contents')
<style>
    .program-row {
        cursor: pointer;
        transition: all 0.2s ease-in-out;
    }

    .program-row:hover {
        background-color: #f8f9fa; /* light gray hover */
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        transform: translateY(-1px);
        text-decoration: none;
    }
</style>
<div class="container-xxl container-p-y">

    {{-- ================= HEADER ================= --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>
            <a href="{{ route('admin.accreditation.index') }}">
                <span class="text-muted fw-light">Accreditation</span>
            </a>
            / View Details
        </h4>

        <div class="d-flex gap-2">
            @if ($isAdmin)
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editAccreditationModal">
                    <i class="bx bx-edit me-1"></i> Edit Accreditation
                </button>
            @endif
        </div>
    </div>

    {{-- ================= ACCREDITATION SUMMARY ================= --}}
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">

                    <div class="d-flex align-items-center mb-3">
                        <i class="bx bx-certification bx-lg text-primary me-3"></i>
                        <div>
                            <h5 class="mb-0" id="accreditationTitle">{{ $accreditation->title }}</h5>
                            <small class="text-muted">Accreditation ID: #{{ $accreditation->id }}</small>
                        </div>
                    </div>

                    <div class="row g-3 mt-2">
                        <div class="col-md-6">
                            <small class="text-muted d-block">Accreditation Body</small>
                            <span class="fw-semibold" id="accreditationBody">{{ $accreditation->accreditationBody->name }}</span>
                        </div>

                        <div class="col-md-6">
                            <small class="text-muted d-block">Visit Type</small>
                            <span class="badge bg-label-info text-capitalize" id="visitType">
                                {{ $accreditation->visit_type }}
                            </span>
                        </div>

                        <div class="col-md-6">
                            <small class="text-muted d-block">Accreditation Date</small>
                            <span class="fw-semibold" id="accreditationDate">
                                {{ optional($accreditation->accreditation_date)->format('F d, Y') }}
                            </span>
                        </div>

                        <div class="col-md-6">
                            <small class="text-muted d-block">Status</small>
                            <span class="badge bg-label-success text-capitalize" id="accreditationStatus">
                                {{ $accreditation->status->value }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ================= QUICK STATS ================= --}}
        <div class="col-lg-4">
            <div class="row g-3">

                <div class="col-12">
                    <div class="card bg-primary text-white">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <small>Total Programs</small>
                                <h4 class="mb-0" id="totalProgramsCount">
                                    {{ $levels->sum(fn($items) => $items->count()) }}
                                </h4>
                            </div>
                            <i class="bx bx-collection bx-lg opacity-75"></i>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card bg-success text-white">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <small>Levels Covered</small>
                                <h4 class="mb-0">{{ $levels->count() }}</h4>
                            </div>
                            <i class="bx bx-layer bx-lg opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ================= LEVELS & PROGRAMS ================= --}}
    <div class="card">
        <h5 class="card-header">
            <i class="bx bx-layer me-2"></i> Levels & Programs
        </h5>

        <div class="card-body">
            <div class="accordion" id="levelsAccordion">

                @foreach($levels as $levelId => $items)
                    @php
                        $level = $items->first()->level;
                    @endphp

                    <div class="accordion-item mb-2">
                        <h2 class="accordion-header" id="level{{ $level->id }}">
                            <button class="accordion-button collapsed"
                                    type="button"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#collapse{{ $level->id }}">
                                <span class="fw-semibold level-title"
                                      data-level-id="{{ $level->id }}">
                                    {{ $items->first()->level_label ?? $level->level_name }}
                                </span>
                                <span class="badge bg-label-primary ms-3 programs-count-badge"
                                    data-level-id="{{ $level->id }}">
                                    {{ $items->count() }} {{ Str::plural('Program', $items->count()) }}
                                </span>
                            </button>
                        </h2>

                        <div id="collapse{{ $level->id }}" class="accordion-collapse collapse">
                            <div class="accordion-body pt-2">

                                {{-- PROGRAM LIST --}}
                                <div class="list-group list-group-flush">
                                    @foreach($items as $mapping)
                                        <a href="{{ route('admin.accreditations.program', [
                                                'infoId' => $accreditation->id,
                                                'levelId' => $level->id,
                                                'programName' => $mapping->program->program_name
                                            ]) }}"
                                            class="list-group-item list-group-item-action d-flex justify-content-between align-items-center program-row"
                                            title="View Areas">
                                            
                                            <span>{{ $mapping->program->program_name }}</span>

                                            <div class="d-flex gap-2">
                                                @if ($isAdmin)
                                                    <button class="btn btn-xs btn-outline-primary edit-program-btn"
                                                            title="Edit Program"
                                                            data-mapping-id="{{ $mapping->id }}"
                                                            data-current-name="{{ $mapping->program->program_name }}">
                                                        <i class="bx bx-edit"></i>
                                                    </button>

                                                    <button type="button"
                                                            title="Delete Program"
                                                            class="btn btn-xs btn-outline-danger delete-program-btn"
                                                            data-mapping-id="{{ $mapping->id }}"
                                                            data-program-name="{{ $mapping->program->program_name }}">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </a>
                                    @endforeach
                                </div>

                                {{-- ADD PROGRAM --}}
                                @if ($isAdmin)
                                    <form class="mt-3 add-program-form">
                                        @csrf
                                        <input type="hidden" name="accreditation_info_id" value="{{ $accreditation->id }}">
                                        <input type="hidden" name="level_id" value="{{ $level->id }}">

                                        <div class="input-group">
                                            <input type="text"
                                                name="program_name"
                                                class="form-control"
                                                placeholder="New Program"
                                                required>
                                            <button class="btn btn-primary">
                                                <i class="bx bx-plus"></i>
                                            </button>
                                        </div>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- ================= EDIT ACCREDITATION MODAL ================= --}}
<div class="modal fade" id="editAccreditationModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form id="editAccreditationForm" class="modal-content">
          @csrf
          @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title">Edit Accreditation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="row g-3">

                    {{-- TITLE --}}
                    <div class="col-md-6">
                        <label class="form-label">Accreditation Title</label>
                        <input type="text"
                               name="title"
                               class="form-control"
                               value="{{ $accreditation->title }}"
                               required>
                    </div>

                    {{-- ACCREDITATION BODY (NEW) --}}
                    <div class="col-md-6">
                        <label class="form-label">Accreditation Body</label>
                        <input type="text"
                               name="accreditation_body"
                               class="form-control"
                               value="{{ $accreditation->accreditationBody->name }}"
                               required>
                    </div>

                    {{-- ACCREDITATION DATE --}}
                    <div class="col-md-6">
                        <label class="form-label">Accreditation Date</label>
                        <input type="date"
                               name="date"
                               class="form-control"
                               id="accreditationDate"
                               value="{{ optional($accreditation->accreditation_date)->format('Y-m-d') }}">
                    </div>

                    {{-- VISIT TYPE --}}
                    <div class="col-md-6">
                        <label class="form-label">Visit Type</label>
                        <select name="visit_type" class="form-select" required>
                            <option value="physical" @selected($accreditation->visit_type === 'physical')>
                                Physical
                            </option>
                            <option value="online" @selected($accreditation->visit_type === 'online')>
                                Online
                            </option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button"
                        class="btn btn-outline-secondary"
                        data-bs-dismiss="modal">
                    Cancel
                </button>
                <button class="btn btn-primary">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ================= EDIT PROGRAM MODAL ================= --}}
<div class="modal fade" id="editProgramModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form id="editProgramForm" class="modal-content">
            @csrf
            @method('PUT')

            <input type="hidden" name="mapping_id" id="editProgramMappingId">

            <div class="modal-header">
                <h5 class="modal-title">Edit Program</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <label class="form-label">Program Name</label>
                <input type="text"
                       name="program_name"
                       id="editProgramName"
                       class="form-control"
                       required>
            </div>

            <div class="modal-footer">
                <button type="button"
                        class="btn btn-outline-secondary"
                        data-bs-dismiss="modal">
                    Cancel
                </button>
                <button class="btn btn-primary">
                    Save
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ================= DELETE PROGRAM MODAL ================= --}}
<div class="modal fade" id="deleteProgramModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title text-danger">
                    Delete Program
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <p class="mb-0">
                    Are you sure you want to delete
                    <strong id="deleteProgramName"></strong>?
                </p>
                <small class="text-muted">
                    This action cannot be undone.
                </small>

                <input type="hidden" id="deleteProgramMappingId">
            </div>

            <div class="modal-footer">
                <button type="button"
                        class="btn btn-outline-secondary"
                        data-bs-dismiss="modal">
                    Cancel
                </button>
                <button type="button"
                        class="btn btn-danger"
                        id="confirmDeleteProgram">
                    Delete
                </button>
            </div>

        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function () {

    // ================= EDIT ACCREDITATION =================
    $('#editAccreditationForm').on('submit', function (e) {
        e.preventDefault();

        let formData = new FormData(this);

        $.ajax({
            url: "{{ route('admin.accreditations.update', $accreditation->id) }}",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            success: function (res) {
                $('#accreditationTitle').text(res.data.title);
                $('#accreditationBody').text(res.data.accreditation_body);
                $('#visitType').text(res.data.visit_type);
                $('#accreditationDate').text(res.data.accreditation_date);

                $('#editAccreditationModal').modal('hide');
                showToast(res.message);
            },
            error: function () {
                showToast('Something went wrong.', 'error');
            }
        });
    });

    // ================= ADD PROGRAM =================
    $(document).on('submit', '.add-program-form', function (e) {
        e.preventDefault();

        let form = $(this);
        let formData = new FormData(this);

        $.ajax({
            url: "{{ route('admin.accreditations.program.store') }}",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            success: function (res) {
                let newItem = `
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <span>${res.data.program_name}</span>

                        <div class="d-flex gap-2">
                            <button class="btn btn-xs btn-outline-primary edit-program-btn"
                                    data-mapping-id="${res.data.mapping_id}"
                                    data-current-name="${res.data.program_name}">
                                <i class="bx bx-edit"></i>
                            </button>

                            <button class="btn btn-xs btn-outline-danger delete-program-btn"
                                    data-mapping-id="${res.data.mapping_id}"
                                    data-program-name="${res.data.program_name}">
                                <i class="bx bx-trash"></i>
                            </button>
                        </div>
                    </div>
                `;

                form.closest('.accordion-body')
                    .find('.list-group')
                    .append(newItem);

                form.trigger('reset');
                showToast(res.message);
            },
            error: function (xhr) {
                if (xhr.status === 422 && xhr.responseJSON?.message) {
                    showToast(xhr.responseJSON.message, 'warning');
                } else {
                    showToast('Failed to add program.', 'error');
                }
            }
        });
    });

    // ================= EDIT PROGRAM OPEN MODAL =================
    $(document).on('click', '.edit-program-btn', function () {
        $('#editProgramMappingId').val($(this).data('mapping-id'));
        $('#editProgramName').val($(this).data('current-name'));
        $('#editProgramModal').modal('show');
    });

    // ================= EDIT PROGRAM SUBMIT =================
    $('#editProgramForm').on('submit', function (e) {
        e.preventDefault();

        let mappingId = $('#editProgramMappingId').val();
        let formData = new FormData(this);
        formData.append('_method', 'PATCH');

        $.ajax({
            url: `/admin/accreditations/program/${mappingId}`,
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            success: function (res) {
                $('[data-mapping-id="' + res.data.mapping_id + '"]')
                    .closest('.list-group-item')
                    .find('span:first')
                    .text(res.data.program_name);

                $('#editProgramModal').modal('hide');
                showToast(res.message);
            },
            error: function () {
                showToast('Something went wrong.', 'error');
            }
        });
    });

    // ================= DELETE PROGRAM OPEN MODAL =================
    $(document).on('click', '.delete-program-btn', function () {
        $('#deleteProgramMappingId').val($(this).data('mapping-id'));
        $('#deleteProgramName').text($(this).data('program-name'));
        $('#deleteProgramModal').modal('show');
    });

    // ================= CONFIRM DELETE PROGRAM =================
    $('#confirmDeleteProgram').on('click', function () {
        let mappingId = $('#deleteProgramMappingId').val();

        $.ajax({
            url: `/admin/accreditations/program/${mappingId}`,
            type: 'POST',
            data: {
                _method: 'DELETE',
                _token: '{{ csrf_token() }}'
            },
            headers: {
                'Accept': 'application/json'
            },
            success: function (res) {
                $('[data-mapping-id="' + mappingId + '"]')
                    .closest('.list-group-item')
                    .fadeOut(300, function () {
                        $(this).remove();
                    });

                $('#deleteProgramModal').modal('hide');
                showToast(res.message);
            },
            error: function () {
                showToast('Failed to delete program.', 'error');
            }
        });
    });

});
</script>
@endpush


