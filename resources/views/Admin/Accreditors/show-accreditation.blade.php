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
                            <h5 class="mb-0">{{ $accreditation->title }}</h5>
                            <small class="text-muted">Accreditation ID: #{{ $accreditation->id }}</small>
                        </div>
                    </div>

                    <div class="row g-3 mt-2">
                        <div class="col-md-6">
                            <small class="text-muted d-block">Accreditation Body</small>
                            <span class="fw-semibold">{{ $accreditation->accreditationBody->name }}</span>
                        </div>

                        <div class="col-md-6">
                            <small class="text-muted d-block">Visit Type</small>
                            <span class="badge bg-label-info text-capitalize">
                                {{ $accreditation->visit_type }}
                            </span>
                        </div>

                        <div class="col-md-6">
                            <small class="text-muted d-block">Accreditation Date</small>
                            <span class="fw-semibold">
                                {{ optional($accreditation->accreditation_date)->format('F d, Y') }}
                            </span>
                        </div>

                        <div class="col-md-6">
                            <small class="text-muted d-block">Status</small>
                            <span class="badge bg-label-success text-capitalize">
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
                                <span class="fw-semibold">{{ $level->level_name }}</span>
                                <span class="badge bg-label-primary ms-3">
                                    {{ $items->count() }} Programs
                                </span>
                            </button>
                        </h2>

                        <div id="collapse{{ $level->id }}" class="accordion-collapse collapse">
                            <div class="accordion-body pt-2">

                                {{-- LEVEL ACTIONS --}}
                                <div class="d-flex justify-content-end gap-2 mb-3">
                                    <button class="btn btn-sm btn-outline-primary">
                                        <i class="bx bx-edit"></i> Edit Level
                                    </button>
                                </div>

                                {{-- PROGRAM LIST --}}
                                <div class="list-group list-group-flush">
                                    @foreach($items as $mapping)
                                        <div class="list-group-item d-flex justify-content-between align-items-center">
                                            <span>{{ $mapping->program->program_name }}</span>

                                            <div class="d-flex gap-2">
                                                <button class="btn btn-xs btn-outline-primary">
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
@endsection

@push('scripts')
<script>
document.getElementById('editAccreditationForm').addEventListener('submit', async function (e) {
    e.preventDefault();

    const form = e.target;
    const formData = new FormData(form);

    try {
        const response = await fetch(
            "{{ route('admin.accreditations.update', $accreditation->id) }}",
            {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: formData
            }
        );

        const data = await response.json();

        if (!response.ok) throw data;

        // Success
        showToast(data.message);

        // Close modal
        bootstrap.Modal.getInstance(
            document.getElementById('editAccreditationModal')
        ).hide();

        // Reload page to reflect changes
        location.reload();

    } catch (error) {
        if (error.errors) {
            showToast(Object.values(error.errors).flat().join('\n'), 'error');
        } else {
            showToast('Something went wrong.', 'error');
        }
    }
});
</script>
@endpush

