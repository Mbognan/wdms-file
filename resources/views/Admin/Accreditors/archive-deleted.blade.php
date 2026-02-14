@extends('admin.layouts.master')

@section('contents')

<div class="container-xxl container-p-y">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold">
            Deleted Programs
        </h4>
    </div>

    {{-- TABLE --}}
    <div class="card">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Accreditation</th>
                        <th>Program</th>
                        <th>Level</th>
                        <th>Deleted By</th>
                        <th>Deleted At</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>

                <tbody>
                @forelse($deletedPrograms as $item)
                    <tr>
                        <td>
                            {{ $item->accreditationInfo->title ?? '—' }} {{ $item->accreditationInfo->year }}
                        </td>

                        <td>
                            {{ $item->program->program_name ?? '—' }}
                        </td>

                        <td>
                            {{ $item->level->level_name ?? '—' }}
                        </td>

                        <td>
                            @if ($item->deletedBy)
                                {{ $item->deletedBy->name }}
                                <span class="badge bg-success">
                                    {{ ucfirst(strtolower($item->deletedBy->user_type->name ?? $item->deletedBy->user_type)) }}
                                </span>
                            @else
                                <span class="text-muted">System</span>
                            @endif
                        </td>
                        <td>
                            {{ $item->deleted_at->format('M d, Y h:i A') }}
                        </td>

                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                {{-- View --}}
                                <a href="#"
                                class="btn btn-sm btn-outline-secondary"
                                title="View Archive">
                                    <i class="bx bx-show"></i>
                                </a>

                                {{-- Restore --}}
                                <a href="#"
                                class="btn btn-sm btn-outline-success"
                                title="Restore Program">
                                    <i class="bx bx-revision"></i>
                                </a>

                                {{-- Restore --}}
                                <a href="#"
                                class="btn btn-sm btn-outline-danger"
                                title="Permanently Program">
                                    <i class="bx bx-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            <i class="bx bx-folder-open"></i>
                            No deleted records found.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
