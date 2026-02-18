@extends('admin.layouts.master')

@section('contents')
<div class="container-xxl container-p-y">

    {{-- HEADER --}}
    <h4 class="fw-bold mb-1">
        {{ $area->area_name }}

        {{-- Final Evaluation Badge --}}
        @if($evaluation?->is_final)
            <span class="badge bg-success ms-2">Final Evaluation</span>
        @endif
    </h4>
    <p class="text-muted mb-4">Submitted Area Evaluation</p>

    {{-- LOCKED / DRAFT NOTICE --}}
    @if($evaluation?->is_final)
        <div class="alert alert-success d-flex">
            <i class="bx bx-lock"></i>
            This is the&nbsp;<strong>Final Evaluation</strong>.
        </div>
    @elseif(
        auth()->user()->user_type === \App\Enums\UserType::INTERNAL_ASSESSOR &&
        $evaluation?->evaluated_by === auth()->id()
    )
        <div class="alert alert-info">
            <i class="bx bx-info-circle"></i>
            Evaluation is saved but not finalized.
        </div>
    @endif

    {{-- MARK AS FINAL BUTTON --}}
    @if(
        auth()->user()->user_type === \App\Enums\UserType::INTERNAL_ASSESSOR &&
        $evaluation?->evaluated_by === auth()->id() &&
        !$evaluation?->is_final
    )
        <div class="mb-3">
            <button 
                type="button" 
                class="btn btn-warning mb-3"
                data-bs-toggle="modal"
                data-bs-target="#finalEvaluationModal"
            >
                Mark as Final Evaluation
            </button>
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-body">

            {{-- EVALUATION TABLE --}}
            <table class="table table-bordered table-sm align-middle">
                <thead class="table-light">
                    <tr class="text-center">
                        <th style="width:35%">Checklist Item</th>
                        <th>Available</th>
                        <th>Available but Inadequate</th>
                        <th>Not Available</th>
                        <th>Not Applicable</th>
                    </tr>
                </thead>

                <tbody>
                @foreach($parameters as $parameter)

                    {{-- PARAMETER HEADER --}}
                    <tr class="table-secondary fw-semibold">
                        <td colspan="5">{{ $parameter->parameter_name }}</td>
                    </tr>

                    @foreach($parameter->sub_parameters as $sub)
                        @php
                            $rating = $ratings[$sub->id] ?? null;
                            $label  = $rating?->ratingOption?->label;
                        @endphp

                        <tr>
                            <td style="padding-left: 30px">{{ $sub->sub_parameter_name }}</td>

                            <td class="text-center">
                                {{ $label === 'Available' ? $rating->score : ' ' }}
                            </td>

                            <td class="text-center">
                                {{ $label === 'Available but Inadequate' ? $rating->score : ' ' }}
                            </td>

                            <td class="text-center">
                                {{ $label === 'Not Available' ? '0' : ' ' }}
                            </td>

                            <td class="text-center">
                                {{ $label === 'Not Applicable' ? 'NA' : ' ' }}
                            </td>
                        </tr>
                    @endforeach

                @endforeach
                </tbody>

                {{-- TOTALS --}}
                <tfoot class="fw-semibold">
                    <tr>
                        <td>Total</td>
                        <td class="text-center">{{ $totals['available'] }}</td>
                        <td class="text-center">{{ $totals['inadequate'] }}</td>
                        <td class="text-center">{{ $totals['not_available'] }}</td>
                        <td class="text-center">{{ $totals['not_applicable'] }}</td>
                    </tr>
                    <tr>
                        <td>Area Mean</td>
                        <td colspan="4" class="text-center fs-5 fw-bold">
                            {{ $mean }}
                        </td>
                    </tr>
                </tfoot>
            </table>

            {{-- RECOMMENDATION --}}
            <div class="mt-4">
                <label class="fw-bold">Recommendations</label>
                <textarea class="form-control no-resize" rows="4" disabled>
{{ $evaluation->areaRecommendations->first()?->recommendation }}
                </textarea>
            </div>

            {{-- ACTIONS --}}
            <div class="mt-4 text-end">
                <a href="{{ url()->previous() }}" class="btn btn-secondary">
                    Back
                </a>
                <button onclick="window.print()" class="btn btn-outline-primary">
                    Print
                </button>
            </div>

            {{-- PREVIOUS / NEXT AREA NAVIGATION --}}
            <div class="mt-4 d-flex justify-content-between">
                @if($prevArea)
                    <a
                        href="{{ route('program.areas.evaluations.summary', ['evaluation' => $evaluation->id, 'area' => $prevArea->id]) }}"
                        class="btn btn-outline-secondary"
                    >
                        ← {{ $prevArea->area_name }}
                    </a>
                @else
                    <span></span>
                @endif

                @if($nextArea)
                    <a
                        href="{{ route('program.areas.evaluations.summary', ['evaluation' => $evaluation->id, 'area' => $nextArea->id]) }}"
                        class="btn btn-outline-primary"
                    >
                        {{ $nextArea->area_name }} →
                    </a>
                @endif
            </div>

        </div>
    </div>
    <x-modal id="finalEvaluationModal" title="Confirm Final Evaluation" :backdrop="false">
        <p>
            Are you sure you want to mark this as 
            <strong>Final Evaluation</strong>?
        </p>

        <p class="text-muted mb-2">
            Once marked as <strong>Final</strong>, this evaluation will be locked 
            and can no longer be edited.
        </p>

        <x-slot name="footer">
            <button 
                type="button" 
                class="btn btn-secondary"
                data-bs-dismiss="modal"
            >
                Cancel
            </button>

            <form 
                action="{{ route('evaluations.finalize', $evaluation) }}" 
                method="POST"
            >
                @csrf
                @method('PATCH')

                <button type="submit" class="btn btn-warning">
                    Yes, Mark as Final
                </button>
            </form>
        </x-slot>
    </x-modal>
</div>

<style>
@media print {

    /* Page setup */
    @page {
        size: A4 portrait;
        margin: 15mm;
    }

    body {
        font-size: 11px;
        color: #000;
        background: #fff;
    }

    /* Hide UI-only elements */
    .btn,
    .alert,
    nav,
    footer {
        display: none !important;
    }

    /* Table layout like accreditation form */
    table {
        width: 100%;
        border-collapse: collapse;
        font-size: 10.5px;
    }

    th, td {
        border: 1px solid #000;
        padding: 4px;
        vertical-align: top;
    }

    th {
        text-align: center;
        font-weight: bold;
    }

    /* Section headers (A, B, C) */
    .section-row {
        font-weight: bold;
        background: #f2f2f2;
    }

    /* Sub-items (A.1, A.2...) */
    .sub-row td:first-child {
        padding-left: 12px;
    }

    /* Totals */
    .total-row {
        font-weight: bold;
    }

    /* Recommendations area */
    .recommendation-box {
        border: 1px solid #000;
        height: 120px;
        margin-top: 10px;
        padding: 5px;
    }
}
</style>

@endsection
