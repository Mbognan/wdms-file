@extends('admin.layouts.master')

@section('contents')
<div class="container-xxl container-p-y">

    <h4 class="fw-bold mb-4">Accreditation Archive</h4>

    <p class="text-muted mb-4">
        Browse past accreditation records. Select a folder to view archived programs.
    </p>

    <div class="row g-4">

        {{-- COMPLETED FOLDER --}}
        <div class="col-md-4">
            <a href="{{ route('archive.completed') }}" class="text-decoration-none">
                <div class="card archive-folder shadow-sm h-100">
                    <div class="card-body text-center">

                        <i class="bx bx-folder-open archive-icon text-success"></i>

                        <h5 class="fw-semibold mt-3">
                            Completed
                        </h5>

                        <p class="text-muted small mb-2">
                            Fully accredited programs
                        </p>

                        <span class="badge bg-success-subtle text-success">
                            View Records
                        </span>
                    </div>
                </div>
            </a>
        </div>

        {{-- DELETED / WITHDRAWN FOLDER --}}
        <div class="col-md-4">
            <a  class="text-decoration-none">
                <div class="card archive-folder shadow-sm h-100">
                    <div class="card-body text-center">

                        <i class="bx bx-folder-minus archive-icon text-danger"></i>

                        <h5 class="fw-semibold mt-3">
                           Trash
                        </h5>

                        <p class="text-muted small mb-2">
                            Removed or discontinued programs
                        </p>

                        <span class="badge bg-danger-subtle text-danger">
                            View Records
                        </span>
                    </div>
                </div>
            </a>
        </div>

    </div>

</div>
@endsection
