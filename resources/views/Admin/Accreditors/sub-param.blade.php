@extends('admin.layouts.master')

@section('contents')

@php
    use App\Enums\UserType;
    $user = auth()->user();
@endphp

    <div class="container-xxl container-p-y">

        {{-- Breadcrumb --}}
        <div class="mb-3">
            <a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-secondary">
                ← Back
            </a>
        </div>

        {{-- Header --}}
        <h4 class="fw-bold mb-1">
            {{ $subParameter->sub_parameter_name }}
        </h4>
        <p class="text-muted mb-4">
            Upload documents for this sub-parameter
        </p>
        
        {{-- Upload Card --}}
        <div class="card mb-4">
            @if ($user->user_type === UserType::DEAN 
                    || $user->user_type === UserType::TASK_FORCE
                )
                <div class="card-body">
                    <form
                        action="{{ route('subparam.uploads.store', [
                            'subParameter' => $subParameter->id,
                            'infoId' => $infoId,
                            'levelId' => $levelId,
                            'programId' => $programId,
                            'programAreaId' => $programAreaId,
                        ]) }}"
                        method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Upload Files</label>
                            <input type="file" name="files[]" class="form-control" multiple required>

                            <small class="text-muted">
                                Multiple files allowed (PDF, DOCX, Images).
                            </small>
                        </div>

                        <button class="btn btn-primary">
                            <i class="bx bx-upload me-1"></i> Upload
                        </button>
                    </form>
                </div>  
            @endif
        </div>

        {{-- Uploaded Files --}}
        <div class="card mt-4">
            <div class="card-header d-flex justify-content-between">
                <h6 class="fw-bold mb-0">
                    Uploaded Files ({{ $uploads->count() }})
                </h6>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>File Name</th>
                            <th>Type</th>
                            <th>Uploaded By</th>
                            <th>User Type</th>
                            <th>Uploaded At</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($uploads as $index => $upload)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $upload->file_name }}</td>
                                <td>{{ strtoupper($upload->file_type) }}</td>
                                <td>
                                    {{ $upload->uploader?->name ?? 'Unknown' }}

                                    @if ($upload->uploader && $upload->uploader->id === auth()->id())
                                        <span class="text-muted">(You)</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-label-info">
                                        {{ ucfirst($upload->uploader?->user_type?->value ?? 'N/A') }}
                                    </span>
                                </td>
                                <td>{{ $upload->created_at->format('M d, Y') }}</td>
                                <td class="d-flex gap-1">

                                    {{-- VIEW --}}
                                    <a href="{{ Storage::url($upload->file_path) }}"
                                    target="_blank"
                                    class="btn btn-sm btn-outline-primary">
                                        <i class="bx bx-show"></i>
                                    </a>

                                    {{-- DELETE (only uploader can delete) --}}
                                    @if ($upload->uploader && $upload->uploader->id === auth()->id())
                                        <form action="{{ route('subparam.uploads.destroy', $upload->id) }}"
                                            method="POST">
                                            @csrf
                                            @method('DELETE')

                                            <button class="btn btn-sm btn-outline-danger">
                                                <i class="bx bx-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">
                                    No files uploaded yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
