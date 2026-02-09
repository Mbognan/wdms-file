@extends('admin.layouts.master')

@section('contents')
<div class="container-xxl container-p-y">

    {{-- ================= HEADER ================= --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>
            <span class="text-muted fw-light">Admin / Accreditation /</span>
            View Details
        </h4>

        <div class="d-flex gap-2">
            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
                <i class="bx bx-arrow-back me-1"></i> Back
            </a>

            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editAccreditationModal">
                <i class="bx bx-edit me-1"></i> Edit Accreditation
            </button>
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
                                <h4 class="mb-0">
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
                                <span class="badge bg-label-primary ms-3">
                                    {{ $items->count() }} Programs
                                </span>
                            </button>
                        </h2>

                        <div id="collapse{{ $level->id }}" class="accordion-collapse collapse">
                            <div class="accordion-body pt-2">

                                {{-- PROGRAM LIST --}}
                                <div class="list-group list-group-flush">
                                    @foreach($items as $mapping)
                                        <div class="list-group-item d-flex justify-content-between align-items-center">
                                            <span>{{ $mapping->program->program_name }}</span>

                                            <div class="d-flex gap-2">
                                                <button class="btn btn-xs btn-outline-primary edit-program-btn"
                                                        data-mapping-id="{{ $mapping->id }}"
                                                        data-current-name="{{ $mapping->program->program_name }}">
                                                    <i class="bx bx-edit"></i>
                                                </button>

                                                <form method="POST"
                                                      action="#">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-xs btn-outline-danger">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                {{-- ADD PROGRAM --}}
                                <form class="mt-3"
                                      method="POST"
                                      action="#">
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
@endsection

@push('scripts')
<script>
$(document).ready(function () {

    $('#editAccreditationForm').on('submit', function (e) {
        e.preventDefault();

        let form = $(this);
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

                // Update page instantly
                $('#accreditationTitle').text(res.data.title);
                $('#accreditationBody').text(res.data.accreditation_body);
                $('#visitType').text(res.data.visit_type);
                $('#accreditationDate').text(res.data.accreditation_date);

                // Close modal
                $('#editAccreditationModal').modal('hide');

                showToast(res.message);
            },
            error: function (xhr) {

                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    let messages = Object.values(errors).flat().join('\n');
                    showToast(messages, 'error');
                } else {
                    showToast('Something went wrong.', 'error');
                }
            }
        });
    });

    // ================= EDIT PROGRAM OPEN MODAL =================
    $(document).on('click', '.edit-program-btn', function () {

        let mappingId = $(this).data('mapping-id');
        let currentName = $(this).data('current-name');

        $('#editProgramMappingId').val(mappingId);
        $('#editProgramName').val(currentName);

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
        method: "POST",
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
        error: function (xhr) {
            if (xhr.status === 422) {
                let errors = Object.values(xhr.responseJSON.errors)
                    .flat()
                    .join('\n');
                showToast(errors, 'error');
            } else {
                showToast('Something went wrong.', 'error');
            }
        }
    });
});
});
</script>
@endpush


