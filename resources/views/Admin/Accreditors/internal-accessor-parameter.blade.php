@extends('admin.layouts.master')

@section('contents')
<style>
    .users-grid {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
        gap: 16px;
        text-align: center;
    }

    .user-box {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .user-avatar {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        background: #e5e7eb;
        color: #1e40af;
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 6px;
        box-shadow: 0 2px 6px rgba(0,0,0,.15);
    }

    .user-name {
        font-size: 13px;
        font-weight: 500;
    }
</style>

<div class="container-xxl container-p-y">

    {{-- BACK --}}
    <div class="mb-3">
        <a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-secondary">
            ← Back to Areas
        </a>
    </div>

    {{-- HEADER --}}
    <h4 class="fw-bold mb-1">{{ $programArea->area->area_name }}</h4>
    <p class="text-muted mb-4">Program Area Evaluation</p>

    {{-- ASSIGNED USERS --}}
    <div class="card mb-4">
        <div class="card-body">
            <h6 class="fw-bold mb-3">Assigned Users</h6>

            <div class="users-grid">
                @foreach ($programArea->users as $user)
                    <div class="user-box">
                        <div class="user-avatar">
                            {{ strtoupper(substr($user->name, 0, 2)) }}
                        </div>
                        <div class="user-name">{{ $user->name }}</div>
                        <div class="user-name text-primary">{{ $user->user_type }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- PARAMETERS (REUSED COMPONENT STYLE) --}}
    <div class="card mb-4">
        <div class="card-header">
            <h6 class="fw-bold mb-0">Parameters</h6>
        </div>

        <div class="card-body">
            <div class="accordion" id="parameterAccordion">
                @foreach($parameters as $index => $parameter)
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button {{ $index ? 'collapsed' : '' }}"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#param{{ $parameter->id }}">
                                {{ $parameter->parameter_name }}
                            </button>
                        </h2>

                        <div id="param{{ $parameter->id }}"
                             class="accordion-collapse collapse {{ !$index ? 'show' : '' }}">
                            <div class="accordion-body">

                                @foreach ($parameter->sub_parameters as $sub)
                                    {{-- 🔁 SAME PAGE REDIRECT (NO NEW VIEW) --}}
                                    <a href="{{ route('subparam.uploads.index', [
    'subParameter'   => $sub->id,
    'infoId'         => $infoId,
    'levelId'        => $levelId,
    'programId'      => $programId,
    'programAreaId'  => $programAreaId,
]) }}"
   class="d-flex justify-content-between p-2 border rounded mb-2 text-decoration-none text-dark">

    <span>{{ $sub->sub_parameter_name }}</span>
    <span class="text-muted small">Open</span>
</a>

                                @endforeach

                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- AREA EVALUATION (SAME PAGE) --}}
    <div class="card mb-4">
        <div class="card-body">
            <h6 class="fw-bold mb-3">Area Evaluation</h6>

            {{-- UPLOAD --}}
            <form method="POST"
                  action="{{ route('area.evaluations.store', [
                        $infoId,
                        $levelId,
                        $programId,
                        $programAreaId
                  ]) }}"
                  enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label class="form-label fw-semibold">Status</label>
                    <select name="status" class="form-select" required>
                        <option value="">Select</option>
                        <option value="not_started">Not Started</option>
                        <option value="ongoing">Ongoing</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Upload Files</label>
                    <input type="file" name="files[]" class="form-control" multiple required>
                </div>

                <button class="btn btn-primary">
                    <i class="bx bx-upload me-1"></i> Submit
                </button>
            </form>

            {{-- VIEW FILES --}}
            @if($evaluation)
                <hr>

                <h6 class="fw-bold mb-2">Uploaded Evaluation Files</h6>

                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>File</th>
                            <th>Type</th>
                            <th>Uploaded By</th>
                            <th>View</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($evaluation->files as $file)
                            <tr>
                                <td>{{ $file->file_name }}</td>
                                <td>{{ strtoupper($file->file_type) }}</td>
                                <td>{{ $file->uploader->name ?? 'N/A' }}</td>
                                <td>
                                    <a href=""
                                       class="btn btn-sm btn-outline-secondary">
                                        <i class="bx bx-show"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

</div>
@endsection
