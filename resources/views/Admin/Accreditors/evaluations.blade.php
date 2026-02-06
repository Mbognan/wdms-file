@extends('admin.layouts.master')

@section('contents')

@php
    use App\Enums\UserType;
    $user = auth()->user();
@endphp
<div class="container-xxl container-p-y">

    <h4 class="mb-4">
        Evaluations
    </h4>
    

    @forelse ($evaluations as $group)

        @php
            $first = $group->first();

            $internal = $group->filter(fn ($e) =>
                $e->evaluator->user_type === \App\Enums\UserType::INTERNAL_ASSESSOR
            );

            $accreditor = $group->filter(fn ($e) =>
                $e->evaluator->user_type === \App\Enums\UserType::ACCREDITOR
            );
        @endphp

        {{-- ================= ACCREDITATION CARD ================= --}}
        <div class="card mb-4">
            <div class="card-body">

                {{-- HEADER --}}
                <div class="mb-3">
                    <h5 class="mb-3">
                        <strong>
                          {{ $first->accreditationInfo->title }}
                          {{ $first->accreditationInfo->year }}
                        </strong>
                        {{-- <strong>{{ $first->level->level_name }}</strong>
                        <span class="text-muted fw-light">
                            · {{ $first->accreditationInfo->title ?? '—' }}
                        </span> --}}
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
                    {{-- TABS --}}
                    @if ($user->user_type === UserType::ADMIN)
                        <ul class="nav nav-pills mb-3" role="tablist">
                            <li class="nav-item">
                                <button class="nav-link active"
                                        data-bs-toggle="tab"
                                        data-bs-target="#internal-{{ $loop->index }}"
                                        type="button">
                                    Internal Assessor
                                </button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link"
                                        data-bs-toggle="tab"
                                        data-bs-target="#accreditor-{{ $loop->index }}"
                                        type="button">
                                    Accreditor
                                </button>
                            </li>
                        </ul>
                    @endif

                    {{-- TAB CONTENT --}}
                    <div class="tab-content">

                        {{-- ================= INTERNAL ASSESSOR ================= --}}
                        <div class="tab-pane fade show active"
                             id="internal-{{ $loop->index }}">

                            <table class="table table-bordered table-sm align-middle">
                                <thead class="table-light text-center">
                                <tr>
                                    <th>Area</th>
                                    <th>Assessor</th>
                                    <th>Status</th>
                                    <th width="120">Action</th>
                                </tr>
                                </thead>

                                <tbody>
                                @forelse ($internal as $evaluation)
                                    @foreach ($evaluation->areaRecommendations as $rec)
                                        <tr>
                                            <td>{{ $rec->area->area_name }}</td>
                                            <td>
                                                {{ $evaluation->evaluator->name }}
                                                @if ($evaluation->evaluator->id === $user->id)
                                                    <span class="text-muted fw-semibold">(You)</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <span class="badge {{ $evaluation->was_updated ? 'bg-warning text-dark' : 'bg-success' }}">
                                                    {{ $evaluation->was_updated ? 'Updated' : 'Submitted' }}
                                                </span>

                                                <div class="small text-muted">
                                                    Submitted: {{ $evaluation->created_at->format('M d, Y h:i A') }}
                                                </div>

                                                @if ($evaluation->was_updated)
                                                    <div class="small text-muted">
                                                        Updated: {{ $evaluation->updated_at->format('M d, Y h:i A') }}
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route(
                                                    'program.areas.evaluation.summary',
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
                        </div>

                        {{-- ================= ACCREDITOR ================= --}}
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
                                            <td>{{ $rec->area->name }}</td>
                                            <td>{{ $evaluation->evaluator->name }}</td>
                                            <td class="text-center">
                                                <span class="badge bg-success">Submitted</span>
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route(
                                                    'program.areas.evaluation.summary',
                                                    [$evaluation->id, $rec->area->id]
                                                ) }}"
                                                   class="btn btn-sm btn-outline-primary">
                                                    View Summary
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

            </div>
        </div>

    @empty
        <div class="alert alert-info">
            No evaluations found.
        </div>
    @endforelse

</div>
@endsection
