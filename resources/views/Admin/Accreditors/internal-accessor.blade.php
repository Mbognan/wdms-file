@extends('admin.layouts.master')

@section('contents')
    <div class="container-xxl flex-grow-1 container-p-y">

        <h4 class="fw-bold py-3 mb-4">Internal Accreditation Overview</h4>

        @if (empty($data))
            <p class="text-muted">No ongoing accreditations available.</p>
        @else
            @foreach ($data as $levelName => $levelInfo)
                <div class="mb-5">

                    <h5 class="fw-bold text-primary mb-3">{{ $levelName }}</h5>

                    <div class="row g-3">

                        @forelse($levelInfo['programs'] as $program)
                            @php
                                $progressColor =
                                    $program['progress'] == 100
                                        ? 'bg-success'
                                        : ($program['progress'] > 0
                                            ? 'bg-warning'
                                            : 'bg-secondary');
                            @endphp

                            <div class="col-md-4">
                                <div class="card h-100 shadow-sm border-0">
                                    <div class="card-body d-flex flex-column justify-content-between">

                                        <div>
                                            <h6 class="card-title mb-1">
                                                {{ $program['program_name'] ?? 'Unnamed Program' }}
                                            </h6>

                                            <p class="text-muted small mb-1">
                                                Accreditation: {{ $program['accreditation_title'] }}
                                            </p>

                                            <p class="small fw-semibold text-info mb-3">
                                                Accreditation Status:
                                                <span class="text-dark">
                                                    {{ $program['accreditation_status_label'] }}
                                                </span>
                                            </p>

                                            {{-- Progress (VISIBLE TO BOTH ACCREDITORS & INTERNAL ACCESSORS) --}}
                                            <small class="text-muted">Evaluation Progress</small>
                                            <div class="progress mb-2" style="height: 10px;">
                                                <div class="progress-bar {{ $progressColor }}"
                                                    style="width: {{ $program['progress'] }}%">
                                                </div>
                                            </div>

                                            <small class="text-muted">
                                                {{ $program['evaluated_areas'] }} / {{ $program['total_areas'] }} areas
                                                evaluated
                                            </small>
                                        </div>

                                        <div class="mt-3">
                                            <a href="{{ route('internal.accessor.program.areas', [
                                                $program['accreditation_id'],
                                                $levelInfo['level_id'],
                                                $program['program_id'],
                                            ]) }}"
                                                class="btn btn-sm btn-outline-primary w-100">
                                                View Program
                                            </a>


                                            @if ($canEvaluate && $program['progress'] == 100)
                                                <button class="btn btn-sm btn-primary w-100 mt-1 open-final-verdict"
                                                    data-program="{{ $program['program_name'] }}"
                                                    data-program-id="{{ $program['program_id'] }}"
                                                    data-accred-info-id="{{ $program['accreditation_id'] }}"
                                                    data-level-id="{{ $levelInfo['level_id'] }}">
                                                    Final Verdict
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                        @empty
                            <div class="col-12">
                                <p class="text-muted fst-italic">
                                    No programs available for this level.
                                </p>
                            </div>
                        @endforelse

                    </div>
                </div>
            @endforeach
        @endif

    </div>

    {{-- ================= FINAL VERDICT MODAL ================= --}}
    <div class="modal fade" id="finalVerdictModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <form id="finalVerdictForm" class="modal-content">

                {{-- Hidden IDs --}}
                <input type="hidden" id="fvProgramId">
                <input type="hidden" id="fvAccredInfoId">
                <input type="hidden" id="fvLevelId">

                <div class="modal-header">
                    <h5 class="modal-title">Final Accreditation Verdict</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <p class="small text-muted mb-3">
                        You are submitting the final accreditation decision for:
                        <br>
                        <strong id="fvProgram"></strong>
                    </p>

                    {{-- STATUS --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Accreditation Status</label>
                        <select class="form-select" id="fvStatus" required>
                            <option value="" selected disabled>Select status</option>
                            <option value="revisit">Revisit</option>
                            <option value="completed">Completed / Granted</option>
                        </select>
                    </div>

                    {{-- REVISIT YEAR (ONLY IF REVISIT) --}}
                    <div class="mb-3 d-none" id="revisitYearContainer">
                        <label class="form-label fw-semibold">
                            Revisit Until (Year)
                        </label>
                        <input type="number" class="form-control" id="fvRevisitYear" min="2022" max="2255"
                            placeholder="e.g. 2028">
                        <div class="form-text text-muted">
                            Select the year when the program must be revisited.
                        </div>
                    </div>

                    {{-- COMMENTS --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Comments / Justification</label>
                        <textarea class="form-control" id="fvComments" rows="4" placeholder="Provide justification for your decision..."
                            required></textarea>
                    </div>

                    <div class="alert alert-warning small mt-3">
                        ⚠️ This action finalizes the accreditation evaluation.
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        Submit Final Verdict
                    </button>
                </div>

            </form>
        </div>
    </div>


@endsection
@push('scripts')
    <script>
        /* ================= FINAL VERDICT MODAL ================= */

        $(document).on('click', '.open-final-verdict', function () {

    $('#fvProgram').text($(this).data('program'));

    $('#fvProgramId').val($(this).data('program-id'));
    $('#fvAccredInfoId').val($(this).data('accred-info-id'));
    $('#fvLevelId').val($(this).data('level-id'));

    $('#fvStatus').val('');
    $('#fvComments').val('');
    $('#fvRevisitYear').val('');
    $('#revisitYearContainer').addClass('d-none');

    $('#finalVerdictModal').modal('show');
});


    $('#fvStatus').on('change', function () {
    if ($(this).val() === 'revisit') {
        $('#revisitYearContainer').removeClass('d-none');
        $('#fvRevisitYear').prop('required', true);
    } else {
        $('#revisitYearContainer').addClass('d-none');
        $('#fvRevisitYear').val('').prop('required', false);
    }
});


        /* Submit (UI ONLY) */
        $('#finalVerdictForm').on('submit', function(e) {
            e.preventDefault();

            if (!$('#fvStatus').val() || !$('#fvComments').val().trim()) {
                alert('Please complete all required fields.');
                return;
            }

            $.ajax({
                url: "{{ route('internal.final.verdict.store') }}",
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    program_id: $('#fvProgramId').val(),
                    accred_info_id: $('#fvAccredInfoId').val(),
                    level_id: $('#fvLevelId').val(),
                    status: $('#fvStatus').val(),
                    comments: $('#fvComments').val()
                },
                success: function() {
                    $('#finalVerdictModal').modal('hide');
                    showToast('Final verdict saved successfully.', 'success');
                    location.reload(); // refresh to reflect status
                },
                error: function() {
                    showToast('Failed to save final verdict.', 'danger');
                }
            });
        });
    </script>
@endpush
