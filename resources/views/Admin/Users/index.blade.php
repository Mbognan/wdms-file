@extends('admin.layouts.master')

@section('contents')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fomantic-ui/2.9.2/semantic.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/2.3.5/css/dataTables.semanticui.css">

<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container-xxl flex-grow-1 container-p-y bg-footer-theme">
    <div class="card">
        <div class="card-header">
            <h5>Pending / Suspended Users</h5>
        </div>

        <div class="card-body">
            <table id="users-table" class="table table-bordered table-striped w-100">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>User Type</th>
                        <th>Status</th>
                        <th>Registered At</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="assignRoleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Assign Role</h5>
                <button type="button" class="btn-close"
                        data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <input type="hidden" id="verify-user-id">

                <div class="mb-3">
                    <label class="form-label">User Role</label>
                    <select id="user-role" class="form-select">
                        <option value="">-- Select Role --</option>
                        <option value="TASK FORCE">Task Force</option>
                        <option value="INTERNAL ASSESSOR">Internal Assessor</option>
                        <option value="ACCREDITOR">Accreditor</option>
                    </select>

                    <div id="role-error"
                         class="text-danger small mt-1 d-none">
                        Please select a role.
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary"
                        data-bs-dismiss="modal">
                    Cancel
                </button>

                <button class="btn btn-success" id="confirm-verify">
                    Verify User
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="verifySuccessModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title text-success">
                    Verification Successful
                </h5>
            </div>

            <div class="modal-body text-center">
                <i class="bx bx-check-circle text-success fs-1 mb-3"></i>
                <p class="mb-0" id="success-message">
                    User verified successfully.
                </p>
            </div>

            <div class="modal-footer">
                <button class="btn btn-primary"
                        data-bs-dismiss="modal">
                    OK
                </button>
            </div>

        </div>
    </div>
</div>

@endsection

@push('scripts')

<script src="https://cdn.datatables.net/2.3.5/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/2.3.5/js/dataTables.semanticui.js"></script>

<script>
$(function () {

    const table = $('#users-table').DataTable({
        processing: true,
        ajax: "{{ route('users.data') }}",
        columns: [
            { data: 'id' },
            { data: 'name' },
            { data: 'email' },
            { data: 'user_type' },
            {
                data: 'status',
                render: status => {
                    if (status === 'Pending') return '<span class="badge bg-warning">Pending</span>';
                    if (status === 'Suspended') return '<span class="badge bg-danger">Suspended</span>';
                    return status;
                }
            },
            {
                data: 'created_at',
                render: date => new Date(date).toLocaleString('en-US', {
                    month: 'short',
                    day: 'numeric',
                    year: 'numeric',
                    hour: 'numeric',
                    minute: '2-digit',
                    hour12: true
                })
            },
            {
                data: null,
                orderable: false,
                searchable: false,
                className: 'text-center',
                render: row => `
                    <button class="btn btn-sm btn-success btn-verify"
                            data-id="${row.id}">
                        <i class="bx bx-check"></i>
                    </button>
                `
            }
        ]
    });

    // OPEN ASSIGN ROLE MODAL
    $(document).on('click', '.btn-verify', function () {
        $('#verify-user-id').val($(this).data('id'));
        $('#user-role').val('');
        $('#role-error').addClass('d-none');

        new bootstrap.Modal('#assignRoleModal').show();
    });

    // CONFIRM VERIFICATION
    $('#confirm-verify').on('click', function () {
        const userId = $('#verify-user-id').val();
        const role = $('#user-role').val();

        if (!role) {
            $('#role-error').removeClass('d-none');
            return;
        }

        $('#role-error').addClass('d-none');

        $.ajax({
            url: `/users/${userId}/verify`,
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: { user_type: role },

            success: res => {
                bootstrap.Modal.getInstance(
                    document.getElementById('assignRoleModal')
                ).hide();

                $('#success-message').text(res.message);

                new bootstrap.Modal('#verifySuccessModal').show();

                table.ajax.reload(null, false);
            },

            error: xhr => {
                $('#role-error')
                    .removeClass('d-none')
                    .text(xhr.responseJSON?.message ?? 'Verification failed.');
            }
        });
    });
});

</script>

@endpush

