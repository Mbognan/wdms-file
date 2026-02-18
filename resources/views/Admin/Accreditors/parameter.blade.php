@extends('admin.layouts.master')

@section('contents')
    <style>
        /* ================= USERS GRID ================= */
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
            box-shadow: 0 2px 6px rgba(0, 0, 0, .15);
        }

        .user-name {
            font-size: 13px;
            font-weight: 500;
        }

        /* ================= PARAMETER BLOCK ================= */
        .parameter-block {
            border: 1px solid #d1d5db;
            border-radius: 6px;
            padding: 12px;
            margin-bottom: 12px;
        }

        .parameter-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 8px;
        }

        .subparams-container {
            padding-left: 20px;
        }

        .input-group .btn {
            width: 36px;
        }
    </style>

    <div class="container-xxl container-p-y">

        {{-- BACK --}}
        <div class="mb-3">
            <a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-secondary">← Back to Areas</a>
        </div>

        {{-- AREA HEADER --}}
        <h4 class="fw-bold mb-1">{{ $programArea->area->area_name }}</h4>
        <p class="text-muted mb-4">AREA</p>

        {{-- ASSIGNED USERS --}}
        @if (!$isAccreditor)
            <div class="card mb-4">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">
                        Assigned
                        {{ $isAdmin || $isIA ? 'Internal Assessors' : 'Task Forces' }}
                    </h6>
                    <div class="users-grid">
                        @foreach ($assignments as $assignment)
                            <div class="user-box">
                                <div class="user-avatar">
                                    {{ strtoupper(substr($assignment->user->name, 0, 2)) }}
                                </div>

                                <div class="user-name">
                                    {{ $assignment->user->name }}
                                    {{ $loggedInUser->id === $assignment->user->id ? '(You)' : '' }}
                                </div>

                                <div class="user-name text-primary">
                                    {{-- Admin only sees "Internal Assessor", else show role from assignment --}}
                                    {{ ($isAdmin && !$isTaskForce) 
                                        ? $assignment->user->user_type
                                        : strtoupper($assignment->role->value ?? 'INTERNAL ASSESSOR') 
                                    }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        {{-- PARAMETERS CARD --}}
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0">Parameters</h6>
                @if($isAdmin)

                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addParameterModal">
                    <i class="bx bx-plus-circle me-1"></i> Add Parameter
                </button>
                @endif
            </div>

            <div class="card-body">
                <div class="accordion mt-3" id="parameterAccordion">

                    @forelse($parameters as $index => $parameter)
                        <div class="card accordion-item {{ $index === 0 ? 'active' : '' }}">

                            {{-- Header --}}
                            <h2 class="accordion-header" id="heading{{ $parameter->id }}">
                                <button type="button" class="accordion-button {{ $index !== 0 ? 'collapsed' : '' }}"
                                    data-bs-toggle="collapse" data-bs-target="#collapse{{ $parameter->id }}"
                                    aria-expanded="{{ $index === 0 ? 'true' : 'false' }}"
                                    aria-controls="collapse{{ $parameter->id }}">

                                    <div class="d-flex justify-content-between align-items-center w-100 pe-3">
                                        <span class="fw-semibold">
                                            {{ $parameter->parameter_name }}
                                        </span>

                                        <span class="badge bg-label-primary">
                                            {{ $parameter->sub_parameters->count() }}
                                        </span>
                                    </div>
                                </button>
                            </h2>

                            {{-- Body --}}
                            <div id="collapse{{ $parameter->id }}"
                                class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}"
                                data-bs-parent="#parameterAccordion">

                                <div class="accordion-body">

                                    @if ($parameter->sub_parameters->isNotEmpty())
                                        @foreach ($parameter->sub_parameters as $sub)
                                            <div
                                                class="d-flex justify-content-between align-items-center mb-2 p-2 border rounded">

                                                <div class="fw-medium">
                                                    {{ $sub->sub_parameter_name }}
                                                </div>

                                                <a href="{{ route('subparam.uploads.index', [
                                                    'subParameter' => $sub->id,
                                                    'infoId' => $infoId,
                                                    'levelId' => $levelId,
                                                    'programId' => $programId,
                                                    'programAreaId' => $programAreaId,
                                                ]) }}"
                                                    class="btn btn-sm btn-outline-primary">
                                                    Open
                                                </a>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="text-muted fst-italic">
                                            No sub-parameters available.
                                        </div>
                                    @endif

                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-3">
                            No parameters found.
                        </div>

                    @endforelse

                </div>
            </div>
        </div>


    </div>
    {{-- ================= ADD PARAMETERS + SUB-PARAMETERS MODAL ================= --}}
    <div class="modal fade" id="addParameterModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Parameters & Sub-Parameters</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form id="addParametersForm">
                    @csrf
                    <input type="hidden" name="area_id" value="{{ $programArea->area->id }}">
                    <div class="modal-body">
                        <div id="parametersContainer"></div>

                        <button type="button" class="btn btn-outline-primary btn-sm mt-3" id="addParameterBtn">
                            <i class="bx bx-plus-circle me-1"></i> Add Parameter
                        </button>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save All</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            let parameterCount = 0;

            // ================= Add Parameter =================
            $('#addParameterBtn').on('click', function() {
                parameterCount++;
                $('#parametersContainer').append(`
        <div class="parameter-block mb-3 border rounded p-3" data-id="${parameterCount}">
            <div class="d-flex align-items-center mb-2">
                <input type="text" name="parameters[${parameterCount}][name]" class="form-control me-2" placeholder="Parameter Name" required>
                <button type="button" class="btn btn-outline-danger remove-parameter">
                    <i class="bx bx-x"></i>
                </button>
            </div>

            <div class="subparams-container ps-4" data-parameter-id="${parameterCount}"></div>

            <button type="button" class="btn btn-outline-secondary btn-sm add-subparam mt-2" data-parameter-id="${parameterCount}">
                <i class="bx bx-plus-circle me-1"></i> Add Sub-Parameter
            </button>
        </div>
    `);
            });

            // ================= Remove Parameter =================
            $(document).on('click', '.remove-parameter', function() {
                $(this).closest('.parameter-block').remove();
            });

            // ================= Add Sub-Parameter =================
            $(document).on('click', '.add-subparam', function() {
                const paramId = $(this).data('parameter-id');
                const container = $(`.subparams-container[data-parameter-id="${paramId}"]`);
                const subCount = container.children().length + 1;

                container.append(`
        <div class="input-group mb-2">
            <input type="text" name="parameters[${paramId}][sub_parameters][${subCount}]" class="form-control" placeholder="Sub-Parameter Name" required>
            <button type="button" class="btn btn-outline-danger remove-subparam">
                <i class="bx bx-x"></i>
            </button>
        </div>
    `);
            });

            // ================= Remove Sub-Parameter =================
            $(document).on('click', '.remove-subparam', function() {
                $(this).closest('.input-group').remove();
            });

            // ================= Submit Parameters + Sub-Parameters =================
            $('#addParametersForm').on('submit', function(e) {
                e.preventDefault();
                const form = $(this);
                const data = form.serialize();

                $.post("{{ route('program-area.parameters.store', ['areaId' => $programArea->id]) }}", data)
                    .done(function(res) {
                        showToast(res.message || 'Parameters added successfully', 'success');
                        form.trigger('reset');
                        $('#parametersContainer').empty();
                        parameterCount = 0;
                        $('#addParameterModal').modal('hide');

                        // Optional: refresh parameter list without reload
                        // location.reload();
                    })
                    .fail(function(xhr) {
                        showToast(xhr.responseJSON?.message || 'Something went wrong', 'error');
                    });
            });
        </script>
    @endpush


@endsection
