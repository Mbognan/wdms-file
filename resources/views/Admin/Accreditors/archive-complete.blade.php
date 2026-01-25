@extends('admin.layouts.master')

@section('contents')
<div class="container-xxl container-p-y">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>
            <span class="text-muted fw-light">Archive /</span> Completed Accreditations
        </h4>

        <a href="{{ route('archive.index') }}" class="btn btn-outline-secondary btn-sm">
            ← Back to Archive
        </a>
    </div>

    <div class="card">
        <h5 class="card-header">Completed Accreditations</h5>

        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Accreditation</th>
                        <th>Year</th>
                        <th>Programs</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody class="table-border-bottom-0">

                    @forelse ($accreditations as $info)
                        <tr>
                            {{-- Accreditation --}}
                            <td>
                                <i class="bx bx-certification bx-sm text-success me-2"></i>
                                <span class="fw-medium">
                                    {{ $info->title }}
                                </span>
                            </td>

                            {{-- Year --}}
                            <td>{{ $info->year }}</td>

                            {{-- Programs --}}
                            <td>
                                <span class="badge bg-label-primary">
                                    {{ $info->completed_programs_count }} Programs
                                </span>
                            </td>

                            {{-- Status --}}
                            <td>
                                <span class="badge bg-label-success">
                                    Completed
                                </span>
                            </td>

                            {{-- Actions --}}
                            <td>
                                <div class="dropdown">
                                    <button
                                        type="button"
                                        class="btn p-0 dropdown-toggle hide-arrow"
                                        data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>

                                    <div class="dropdown-menu">

                                        {{-- View Completed Levels + Programs --}}
                                        <a
                                            class="dropdown-item"
                                            href="">
                                            <i class="bx bx-folder-open me-1"></i>
                                            View Completed Details
                                        </a>

                                    </div>
                                </div>
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">
                                No completed accreditations found.
                            </td>
                        </tr>
                    @endforelse

                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
