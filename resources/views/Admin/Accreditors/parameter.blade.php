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
                                    <div class="user-name justify-content-between align-items-center">
                                        @if($isDean || $isTaskForce)
                                            {{-- Show role as badge for Dean / Task Force --}}
                                            <span class="badge bg-label-primary mb-2">
                                                {{ strtoupper($assignment->role?->value ?? 'MEMBER') }}
                                            </span>
                                        @endif

                                        @if($isAdmin || $isDean)
                                            {{-- Unassign button --}}
                                            <form action="#" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to unassign {{ $assignment->user->name }}?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger d-flex align-items-center mt-2">
                                                    <i class="bx bx-user-x me-1"></i> Unassign
                                                </button>
                                            </form>
                                        @endif
                                    </div>
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
                    <div class="d-flex gap-2">
                        @if ($parameters->count() > 0)
                            <button class="btn btn-outline-secondary btn-sm"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editParameterModal">
                                <i class="bx bx-edit"></i> Edit
                            </button>

                            <button class="btn btn-outline-danger btn-sm"
                                    data-bs-toggle="modal"
                                    data-bs-target="#deleteParameterModal">
                                <i class="bx bx-trash"></i> Delete
                            </button>
                        @endif
                        
                        <button class="btn btn-primary btn-sm"
                                data-bs-toggle="modal"
                                data-bs-target="#addParameterModal">
                            <i class="bx bx-plus-circle me-1"></i> Add Parameter
                        </button>

                    </div>
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
                                            <div class="d-flex justify-content-between align-items-center mb-2 p-2 border rounded">

                                            <div class="fw-medium">
                                                {{ $sub->sub_parameter_name }}
                                            </div>

                                            <div class="d-flex gap-2">

                                                @if($isAdmin)
                                                    <button class="btn btn-sm btn-outline-secondary edit-subparam-btn"
                                                            data-id="{{ $sub->id }}"
                                                            data-name="{{ $sub->sub_parameter_name }}">
                                                        <i class="bx bx-edit"></i>
                                                        Edit
                                                    </button>

                                                    <button class="btn btn-sm btn-outline-danger delete-subparam-btn"
                                                            data-id="{{ $sub->id }}"
                                                            data-name="{{ $sub->sub_parameter_name }}">
                                                        <i class="bx bx-trash"></i>
                                                        Delete
                                                    </button>
                                                @endif

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

    {{-- ================= EDIT PARAMETERS MODAL ================= --}}
    <div class="modal fade" id="editParameterModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">
                        {{ $parameters->count() > 1 ? 'Edit Parameters' : 'Edit Parameter' }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form id="editParameterForm">
                    @csrf

                    <div class="modal-body d-flex flex-column gap-3">
                        @foreach($parameters as $parameter)
                            <div>
                                <input type="text"
                                    name="parameters[{{ $parameter->id }}]"
                                    value="{{ $parameter->parameter_name }}"
                                    class="form-control"
                                    required>
                            </div>
                        @endforeach
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                            Save Changes
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>

    {{-- ================= DELETE PARAMETERS MODAL ================= --}}
    <div class="modal fade" id="deleteParameterModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Delete Parameters</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form id="deleteParameterForm">
                    @csrf
                    <input type="hidden" name="area_id" value="{{ $programArea->id }}">

                    <div class="modal-body">
                        <p>Select parameters to delete:</p>

                        @foreach($parameters as $parameter)
                            <div class="form-check mb-2">
                                <input class="form-check-input"
                                    type="checkbox"
                                    name="parameters[]"
                                    value="{{ $parameter->id }}"
                                    id="param{{ $parameter->id }}">
                                <label class="form-check-label" for="param{{ $parameter->id }}">
                                    {{ $parameter->parameter_name }}
                                </label>
                            </div>
                        @endforeach
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-danger">
                            Delete Selected
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
    
    {{-- ================= EDIT SUB-PARAMETERS MODAL ================= --}}
    <div class="modal fade" id="editSubParamModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Edit Sub-Parameter</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form id="editSubParamForm">
                    @csrf

                    <input type="hidden" id="editSubParamId" name="sub_parameter_id">

                    <div class="modal-body mb-3">
                        <label class="form-label">Sub-Parameter Name</label>
                        <input type="text" id="editSubParamName" name="sub_parameter_name" class="form-control" required>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                            Save Changes
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>

    {{-- ================= DELETE SUB-PARAMETERS MODAL ================= --}}
    <div class="modal fade" id="deleteSubParamModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Delete Sub-Parameter</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <p>Are you sure you want to delete <strong id="deleteSubParamName"></strong>?</p>
                    <input type="hidden" id="deleteSubParamId" name="sub_parameter_id">
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteSubParam">
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </div>

@push('scripts')
<script>
$(function () {

    const csrfToken = '{{ csrf_token() }}';
    let parameterCount = 0;

    // ================= GLOBAL AJAX SETUP =================
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': csrfToken }
    });

    // =====================================================
    // ADD PARAMETER
    // =====================================================
    $(document).on('click', '#addParameterBtn', function () {
        parameterCount++;

        $('#parametersContainer').append(`
            <div class="parameter-block mb-3 border rounded p-3" data-id="${parameterCount}">
                <div class="d-flex align-items-center mb-2">
                    <input type="text"
                        name="parameters[${parameterCount}][name]"
                        class="form-control me-2"
                        placeholder="Parameter Name"
                        required>

                    <button type="button"
                        class="btn btn-outline-danger remove-parameter">
                        <i class="bx bx-x"></i>
                    </button>
                </div>

                <div class="subparams-container ps-4"
                    data-parameter-id="${parameterCount}">
                </div>

                <button type="button"
                    class="btn btn-outline-secondary btn-sm add-subparam mt-2"
                    data-parameter-id="${parameterCount}">
                    <i class="bx bx-plus-circle me-1"></i>
                    Add Sub-Parameter
                </button>
            </div>
        `);
    });

    // =====================================================
    // REMOVE PARAMETER
    // =====================================================
    $(document).on('click', '.remove-parameter', function () {
        $(this).closest('.parameter-block').remove();
    });

    // =====================================================
    // ADD SUB PARAMETER
    // =====================================================
    $(document).on('click', '.add-subparam', function () {
        const paramId = $(this).data('parameter-id');
        const container = $(`.subparams-container[data-parameter-id="${paramId}"]`);
        const subCount = container.children().length + 1;

        container.append(`
            <div class="input-group mb-2">
                <input type="text"
                    name="parameters[${paramId}][sub_parameters][${subCount}]"
                    class="form-control"
                    placeholder="Sub-Parameter Name"
                    required>

                <button type="button"
                    class="btn btn-outline-danger remove-subparam">
                    <i class="bx bx-x"></i>
                </button>
            </div>
        `);
    });

    // =====================================================
    // REMOVE SUB PARAMETER
    // =====================================================
    $(document).on('click', '.remove-subparam', function () {
        $(this).closest('.input-group').remove();
    });

    // =====================================================
    // GENERIC AJAX FORM HANDLER
    // =====================================================
    function ajaxFormSubmit(formSelector, url, successMessage, modalToClose = null, method = 'POST') {
        $(document).on('submit', formSelector, function(e) {
            e.preventDefault();

            const form = $(this);

            $.ajax({
                url: url,
                type: method,
                data: form.serialize(),
                success: function(res) {
                    showToast(res.message || successMessage, 'success');
                    if (modalToClose) {
                        $(modalToClose).modal('hide');
                    }
                    location.reload();
                },
                error: function(xhr) {
                    showToast(xhr.responseJSON?.message || 'Something went wrong', 'error');
                }
            });
        });
    }

    // =====================================================
    // APPLY AJAX HANDLERS
    // =====================================================

    // Add Parameters (POST)
    ajaxFormSubmit(
        '#addParametersForm',
        "{{ route('program-area.parameters.store', ['areaId' => $programArea->id]) }}",
        'Parameters added successfully',
        '#addParameterModal',
        'POST'
    );

    // Edit Parameters (PATCH)
    ajaxFormSubmit(
        '#editParameterForm',
        "{{ route('parameters.bulk-update') }}",
        'Parameters updated successfully',
        '#editParameterModal',
        'PATCH'
    );

    // Delete Parameters (DELETE)
    ajaxFormSubmit(
        '#deleteParameterForm',
        "{{ route('parameters.bulk-delete') }}",
        'Parameters deleted successfully',
        '#deleteParameterModal',
        'DELETE'
    );

    $('#editSubParamForm').submit(function(e) {
        e.preventDefault();

        const subParamId = $('#editSubParamId').val();

        $.ajax({
            url: '/subparameters/' + subParamId,
            type: 'PATCH',
            data: $(this).serialize(),
            success: function(res) {
                showToast(res.message, 'success');
                $('#editSubParamModal').modal('hide');
                location.reload();
            },
            error: function(xhr) {
                showToast(xhr.responseJSON?.message || 'Something went wrong', 'error');
            }
        });
    });

    $('#confirmDeleteSubParam').click(function() {
        const subParamId = $('#deleteSubParamId').val();

        $.ajax({
            url: '/subparameters/' + subParamId,
            type: 'DELETE',
            data: { _token: '{{ csrf_token() }}' },
            success: function(res) {
                showToast(res.message, 'success');
                $('#deleteSubParamModal').modal('hide');
                location.reload();
            },
            error: function(xhr) {
                showToast(xhr.responseJSON?.message || 'Something went wrong', 'error');
            }
        });
    });

    // =====================================================
    // SUB PARAMETER MODALS
    // =====================================================
    $(document).on('click', '.edit-subparam-btn', function () {
        $('#editSubParamId').val($(this).data('id'));
        $('#editSubParamName').val($(this).data('name'));
        $('#editSubParamModal').modal('show');
    });

    $(document).on('click', '.delete-subparam-btn', function () {
        $('#deleteSubParamId').val($(this).data('id'));
        $('#deleteSubParamName').text($(this).data('name'));
        $('#deleteSubParamModal').modal('show');
    });

});
</script>
@endpush


@endsection
