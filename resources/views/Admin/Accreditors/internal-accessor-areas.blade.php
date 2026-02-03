@extends('admin.layouts.master')

@section('contents')
<style>
    .assigned-users {
        display: flex;
        justify-content: center;
        margin-bottom: 10px;
    }

    .avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: #e5e7eb;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        color: #1e40af;
        font-size: 13px;
        margin-left: -10px;
        border: 2px solid #fff;
    }

    .avatar:first-child {
        margin-left: 0;
    }

    .avatar.more {
        background: #2563eb;
        color: #fff;
    }

    .status-badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
    }

    .status-not_started { background: #6b7280; color: #fff; }
    .status-ongoing     { background: #f59e0b; color: #fff; }
    .status-completed   { background: #16a34a; color: #fff; }
</style>

<div class="container-xxl container-p-y">

    {{-- HEADER --}}
    <div class="mb-4">
        <h4>
            <span class="text-muted fw-light">
                Internal Assessor /
            </span>
            {{ $programName }} – Areas
        </h4>
    </div>

    {{-- PROGRAM CARD --}}
    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0 fw-bold text-center">
                {{ $programName }}
            </h5>
        </div>

        <div class="card-body">
            <p class="text-muted text-center mb-4">
                Select an area to proceed with evaluation.
            </p>

            <div class="row g-3">

                @forelse($programAreas as $mapping)

                    @php
                        // Latest evaluation (loaded in backend)
                        $evaluation = $mapping->evaluations->first();

                        $status = $evaluation->status ?? 'not_started';

                        $statusClass = match($status) {
                            'completed' => 'status-completed',
                            'ongoing' => 'status-ongoing',
                            default => 'status-not_started'
                        };

                        // Internal accessor name (from uploaded file)
                        $evaluatorName = $evaluation?->files->first()?->uploader?->name;
                    @endphp

                    <div class="col-md-4">
                        <a href="{{ route(
                            'program.areas.evaluation',
                            [$infoId, $levelId, $programId, $mapping->area->id]
                        ) }}"
                        class="text-decoration-none text-dark">

                            <div class="card h-100 shadow-sm">

                                {{-- AREA HEADER --}}
                                <div class="text-white text-center py-2 fw-bold rounded-top bg-primary">
                                    {{ $mapping->area->area_name }}
                                </div>

                                <div class="card-body text-center">

                                    {{-- ASSIGNED USERS --}}
                                    <div class="assigned-users">
                                        @foreach ($mapping->users->take(3) as $user)
                                            <div class="avatar">
                                                {{ strtoupper(substr($user->name, 0, 2)) }}
                                            </div>
                                        @endforeach

                                        @if ($mapping->users->count() > 3)
                                            <div class="avatar more">
                                                +{{ $mapping->users->count() - 3 }}
                                            </div>
                                        @endif
                                    </div>

                                    {{-- STATUS --}}
                                    <div class="mb-2">
                                        <span class="status-badge {{ $statusClass }}">
                                            {{ str_replace('_', ' ', ucfirst($status)) }}
                                        </span>
                                    </div>

                                    {{-- EVALUATED BY --}}
                                    <small class="text-muted d-block">
                                        Evaluated by:
                                        <strong>
                                            {{ $evaluatorName ?? '—' }}
                                        </strong>
                                    </small>

                                </div>
                            </div>
                        </a>
                    </div>

                @empty
                    <div class="col-12 text-center">
                        <p class="text-muted fst-italic">
                            No areas available for this program.
                        </p>
                    </div>
                @endforelse

            </div>
        </div>
    </div>
</div>
@endsection
