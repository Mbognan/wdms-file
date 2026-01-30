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
                render: date => {
                    const d = new Date(date);
                    return d.toLocaleString('en-US', {
                        month: 'short',
                        day: 'numeric',
                        year: 'numeric',
                        hour: 'numeric',
                        minute: '2-digit',
                        hour12: true
                    });
                }
            },
            {
                data: null,
                orderable: false,
                searchable: false,
                className: 'text-center align-middle',
                render: row => `
                    <div class="d-flex justify-content-center gap-2">
                        <button class="btn btn-sm btn-success btn-verify"
                                data-id="${row.id}"
                                title="Verify">
                            <i class="bx bx-check"></i>
                        </button>

                        <button class="btn btn-sm btn-danger btn-suspend"
                                data-id="${row.id}"
                                data-url="/users/${row.id}/suspend"
                                title="Suspend">
                            <i class="bx bx-trash"></i>
                        </button>
                    </div>
                `
            }
        ]
    });

    // VERIFY USER WITH ROLE SELECTION
    $(document).on('click', '.btn-verify', function () {
        const id = $(this).data('id');

        Swal.fire({
            title: 'Assign role to this user',
            input: 'select',
            inputOptions: {
                'TASK FORCE': 'Task Force',
                'INTERNAL ASSESSOR': 'Internal Assessor',
                'ACCREDITOR':'Accreditor'
            },
            inputPlaceholder: 'Select a role',
            inputValidator: value => {
                if (!value) return 'You must select a role';
            },
            showCancelButton: true,
            confirmButtonText: 'Verify user',
        }).then(result => {
            if (!result.isConfirmed) return;

            $.ajax({
                url: `/users/${id}/verify`,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    user_type: result.value
                },
                success: res => {
                    table.ajax.reload(null, false);
                    Swal.fire({
                        icon: 'success',
                        title: 'Verified!',
                        text: res.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                },
                error: xhr => {
                    Swal.fire(
                        'Error',
                        xhr.responseJSON?.message ?? 'Verification failed',
                        'error'
                    );
                }
            });
        });
    });

});
</script>

@endpush

