@extends('admin.layouts.master')

@section('contents')

@php
    use App\Enums\UserType;
    $user = auth()->user();

    $subHeader = match ($user->user_type) {
        UserType::TASK_FORCE =>
            "Evaluation of Internal Assessor for areas you're assigned to",

        UserType::INTERNAL_ASSESSOR => 
            "Evaluation you've made",

        UserType::DEAN,
        UserType::ADMIN,
        UserType::ACCREDITOR =>
            'Evaluations of Internal Assessor',

        default => '',
    };
@endphp

<div class="container-xxl container-p-y">

    <h4 class="mb-1">Evaluations</h4>
    <p class="text-muted mb-4">{{ $subHeader }}</p>

    @forelse ($evaluations as $key => $group)

        @php
            $first = $group->first();

            $internal = $group->filter(
                fn ($e) => $e->evaluator->user_type === UserType::INTERNAL_ASSESSOR
            );

            $accreditor = $group->filter(
                fn ($e) => $e->evaluator->user_type === UserType::ACCREDITOR
            );
        @endphp

        <div class="card mb-4">
            <div class="card-body">

                {{-- HEADER --}}
                <div class="mb-3">
                    <h5 class="mb-3 fw-bold">
                        {{ $first->accreditationInfo->title }}
                        {{ $first->accreditationInfo->year }}
                    </h5>

                    <p class="mb-0">
                        Program:
                        <strong>{{ $first->program->program_name }}</strong>
                    </p>
                    <p class="mb-0">
                        Level:
                        <strong>{{ $first->level->level_name }}</strong>
                    </p>
                </div>

                <div class="nav-align-top">

                    <div class="tab-content">

                        {{-- ================= INTERNAL ASSESSOR TAB ================= --}}
                        <div class="tab-pane fade show active"
                             id="internal-{{ $loop->index }}">

                            <table class="table table-bordered table-sm align-middle">
                                <thead class="table-light text-center">
                                    @if (!$isAccreditor)
                                        <tr>
                                            <th>Area</th>
                                            <th>Assessor</th>
                                            <th>Status</th>
                                            <th>Submitted At</th>
                                            <th>Updated At</th>
                                            <th width="120">Action</th>
                                        </tr>
                                    @else
                                        <tr>
                                            <th>Area</th>
                                            <th>Assessor</th>
                                            <th>Submitted At</th>
                                            <th width="120">Action</th>
                                        </tr>
                                    @endif
                                </thead>
                                <tbody>
                                    @forelse ($internal as $evaluation)
                                        @foreach ($evaluation->areaRecommendations as $rec)
                                            <tr>
                                                <td>
                                                    @php
                                                        $areaName = strtoupper($rec->area->area_name);

                                                        $routeName = $isInternalAssessor
                                                            ? 'program.areas.evaluation'
                                                            : 'program.areas.parameters';
                                                    @endphp

                                                    <a href="{{ route($routeName, [
                                                        'infoId'        => $first->accreditationInfo->id,
                                                        'levelId'       => $first->level->id,
                                                        'programId'     => $first->program->id,
                                                        'programAreaId' => $rec->area->id,
                                                    ]) }}"
                                                    class="fw-semibold text-decoration-none link-primary link-underline-opacity-0 link-underline-opacity-100-hover">
                                                        {{ $areaName }}
                                                    </a>
                                                </td>
                                                <td>{{ $evaluation->evaluator->name }}</td>
                                                @if (!$isAccreditor)
                                                    <td class="text-center">
                                                        @if ($evaluation->is_updated)
                                                            <span class="badge bg-warning text-dark">
                                                                Updated
                                                            </span>
                                                        @else
                                                            <span class="badge bg-success">
                                                                Submitted
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        {{ $evaluation->created_at->format('M d, Y h:i A') }}
                                                    </td>
                                                @endif
                                                
                                                <!-- Become the submitted at column if user is accreditor -->
                                                <td class="text-center text-muted">
                                                    {{ $evaluation->is_updated
                                                        ? $evaluation->updated_at->format('M d, Y h:i A')
                                                        : ($isAccreditor
                                                            ? $evaluation->created_at->format('M d, Y h:i A')
                                                            : '—')
                                                    }}
                                                </td>
                                                <td class="text-center">
                                                    <a href="{{ route(
                                                        'program.areas.evaluations.summary',
                                                        [$evaluation->id, $rec->area->id]
                                                    ) }}"
                                                       class="btn btn-sm btn-outline-primary">
                                                        View
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @empty
                                        <tr>
                                            <td colspan="4"
                                                class="text-center text-muted">
                                                No internal assessor evaluation yet
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>

                            {{-- SUMMARY BUTTON (INTERNAL ONLY) --}}
                            @if ($isAdmin || $isDean || $isAccreditor)
                                <div class="d-flex justify-content-end mt-3">
                                    <button class="btn btn-outline-primary"
                                            data-bs-toggle="modal"
                                            data-bs-target="#grandMeanModal-internal-{{ $loop->index }}">
                                        View Summary of Ratings
                                    </button>
                                </div>
                            @endif
                        </div>

                        {{-- ================= ACCREDITOR TAB ================= --}}
                        <div class="tab-pane fade"
                             id="accreditor-{{ $loop->index }}">

                            <table class="table table-bordered table-sm align-middle">
                                <thead class="table-light text-center">
                                    <tr>
                                        <th>Area</th>
                                        <th>Accreditor</th>
                                        <th>Status</th>
                                        <th width="120">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($accreditor as $evaluation)
                                        @foreach ($evaluation->areaRecommendations as $rec)
                                            <tr>
                                                <td>{{ $rec->area->area_name }}</td>
                                                <td>
                                                    {{ $evaluation->evaluator->name }}
                                                    @if ($evaluation->evaluator->id === $user->id)
                                                        <span class="text-muted">(You)</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge {{ $evaluation->was_updated ? 'bg-warning text-dark' : 'bg-success' }}">
                                                        {{ $evaluation->was_updated ? 'Updated' : 'Submitted' }}
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <a href="{{ route(
                                                        'program.areas.evaluations.summary',
                                                        [$evaluation->id, $rec->area->id]
                                                    ) }}"
                                                       class="btn btn-sm btn-outline-primary">
                                                        View
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @empty
                                        <tr>
                                            <td colspan="4"
                                                class="text-center text-muted">
                                                No accreditor evaluation yet
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- ================= INTERNAL GRAND MEAN MODAL ================= --}}
                @php
                    $gm = $grandMeans[$key]['internal'] ?? null;
                    $internalSignatories = $signatories[$key]['internal'] ?? [];
                @endphp

                @if ($gm)
                <div class="modal fade"
                    id="grandMeanModal-internal-{{ $loop->index }}"
                    tabindex="-1"
                    aria-hidden="true">

                    <div class="modal-dialog modal-fullscreen">
                        <div class="modal-content">

                            {{-- HEADER --}}
                            <div class="modal-header justify-content-center position-relative">
                                <h5 class="modal-title fw-bold">
                                    SUMMARY OF RATINGS
                                </h5>
                                <button type="button"
                                        class="btn-close position-absolute end-0 me-3"
                                        data-bs-dismiss="modal"></button>
                            </div>

                            {{-- BODY --}}
                            <div class="modal-body px-4">

                                <table class="table table-bordered align-middle">
                                    <thead class="table-light text-center">
                                        <tr>
                                            <th style="width:70%">Area</th>
                                            <th style="width:30%">Area Mean</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($gm['areaModels'] as $area)
                                            <tr>
                                                <td>
                                                    {{ preg_replace(
                                                        '/^AREA\s+([IVXLC]+)\s*:\s*/i',
                                                        '$1. ',
                                                        strtoupper($area->area_name)
                                                    ) }}
                                                </td>
                                                <td class="text-center">
                                                    {{ number_format($gm['areas'][$area->id] ?? 0, 2) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                                {{-- TOTAL --}}
                                <div class="row mt-4">
                                    <div class="col-6 text-end fw-bold">Total</div>
                                    <div class="col-6 text-center">
                                        {{ number_format($gm['total'], 2) }}
                                    </div>
                                </div>

                                {{-- GRAND MEAN --}}
                                <div class="row mt-2">
                                    <div class="col-6 text-end fw-bold">Grand Mean</div>
                                    <div class="col-6 text-center fw-bold">
                                        {{ number_format($gm['grand'], 2) }}
                                    </div>
                                </div>
                                
                                <!-- 
                                Temporary Adjectives Based on the Grand Mean.
                                 Good practice to save in the database or as enums.
                                 -->
                                @php
                                    $grandMean = $gm['grand'];

                                    if ($grandMean >= 4 && $grandMean <= 5) {
                                        $interpretation = 'Proceed to Level I within 6 months';
                                    } elseif ($grandMean >= 2.5 && $grandMean <= 3.9) {
                                        $interpretation = 'Proceed to Level I not earlier than 1 year';
                                    } elseif ($grandMean >= 1 && $grandMean <= 2.4) {
                                        $interpretation = 'Proceed to Level I not earlier than 2 years';
                                    } else {
                                        $interpretation = 'Conduct another Preliminary Survey Visit';
                                    }
                                @endphp

                                <div class="row mt-3">
                                    <div class="col-6 text-end fw-bold">
                                        Interpretation
                                    </div>
                                    <div class="col-6 text-center fw-semibold text-primary">
                                        {{ $interpretation }}
                                    </div>
                                </div>

                                {{-- SIGNATORIES --}}
                                <div class="row mt-5 justify-content-center">
                                    @foreach ($internalSignatories as $name)
                                        <div class="col-4 text-center">
                                            <div class="fw-semibold">{{ $name }}</div>
                                            <div class="signature-line mx-auto"></div>
                                            <div class="small text-muted">
                                                Internal Assessor
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                            </div>

                            {{-- FOOTER --}}
                            <div class="modal-footer">
                                <button type="button"
                                        class="btn btn-outline-primary"
                                        onclick="printGrandMean('grandMeanModal-internal-{{ $loop->index }}')">
                                    <i class="bx bx-printer me-1"></i> Print
                                </button>

                                <button type="button"
                                        class="btn btn-outline-secondary"
                                        data-bs-dismiss="modal">
                                    Close
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

    @empty
        <div class="alert alert-info">No evaluations found.</div>
    @endforelse
</div>

<style>
.signature-line {
    width: 260px;
    border-bottom: 1.5px solid #000;
    margin-top: 4px;
}
</style>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const restoredGroups = new Set()

    // RESTORE (only once per group)
    document.querySelectorAll('[data-tab-group]').forEach(button => {
        const group = button.dataset.tabGroup

        if (restoredGroups.has(group)) return

        const savedTarget = localStorage.getItem(group)
        if (!savedTarget) return

        const targetButton = document.querySelector(
            `[data-tab-group="${group}"][data-bs-target="${savedTarget}"]`
        )

        if (targetButton) {
            restoredGroups.add(group)
            bootstrap.Tab.getOrCreateInstance(targetButton).show()
        }
    })

    // SAVE
    document.querySelectorAll('[data-bs-toggle="tab"]').forEach(button => {
        button.addEventListener('shown.bs.tab', function (e) {
            const group = e.target.dataset.tabGroup
            const target = e.target.dataset.bsTarget

            if (group && target) {
                localStorage.setItem(group, target)
            }
        })
    })

})
</script>
<script>
function printGrandMean(modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;

    const printContents = modal.querySelector('.modal-content').innerHTML;
    const originalContents = document.body.innerHTML;

    document.body.innerHTML = `
        <html>
            <head>
                <title>Summary of Ratings</title>
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        padding: 40px;
                    }
                    table {
                        width: 100%;
                        border-collapse: collapse;
                    }
                    table, th, td {
                        border: 1px solid #000;
                    }
                    th, td {
                        padding: 8px;
                    }
                    .modal-header button,
                    .modal-footer {
                        display: none !important;
                    }
                    .signature-line {
                        width: 260px;
                        border-bottom: 1.5px solid #000;
                        margin: 6px auto 0;
                    }
                </style>
            </head>
            <body>
                ${printContents}
            </body>
        </html>
    `;

    window.print();
    document.body.innerHTML = originalContents;
    location.reload(); // restore JS & Bootstrap properly
}
</script>
@endpush

@endsection
