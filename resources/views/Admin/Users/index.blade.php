@extends('admin.layouts.master')

@section('contents')

{{-- DataTables CSS --}}
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

{{-- jQuery --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

{{-- DataTables --}}
<script src="https://cdn.datatables.net/2.3.5/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/2.3.5/js/dataTables.semanticui.js"></script>

{{-- SweetAlert2 (CDN if not already global) --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function () {

    $('#users-table').DataTable({
        processing: true,
        ajax: "{{ route('users.data') }}",
        columns: [
            { data: 'id' },
            { data: 'name' },
            { data: 'email' },
            { data: 'user_type' },

            // STATUS BADGE
            {
                data: 'status',
                render: function (status) {
                    if (status === 'Pending') {
                        return '<span class="badge bg-warning">Pending</span>';
                    }
                    if (status === 'Suspended') {
                        return '<span class="badge bg-danger">Suspended</span>';
                    }
                    return status;
                }
            },

            // READABLE DATE
            {
                data: 'created_at',
                render: function (date) {
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

            // ACTION BUTTONS (CENTERED)
            {
                data: null,
                orderable: false,
                searchable: false,
                className: 'text-center align-middle',
                render: function (data, type, row) {
                    return `
                        <div class="d-flex justify-content-center align-items-center gap-2">
                            <button class="btn btn-sm btn-success"
                                    title="Verify"
                                    onclick="verifyUser(${row.id})">
                                <i class="bx bx-check"></i>
                            </button>

                           <button class="btn btn-sm btn-danger btn-suspend"
                                data-id="${row.id}"
                                data-url="/users/${row.id}/suspend"
                                title="Suspend">
                            <i class="bx bx-trash"></i>
                        </button>

                        </div>
                    `;
                }
            }
        ]
    });

});


function verifyUser(id) {
    Swal.fire({
        title: "Verify this user?",
        text: "This user will be activated.",
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#28a745",
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Yes, verify"
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/users/${id}/verify`,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (res) {
                    $('#users-table').DataTable().ajax.reload(null, false);

                    Swal.fire({
                        title: "Verified!",
                        text: res.message,
                        icon: "success",
                        timer: 2000,
                        showConfirmButton: false
                    });
                },
                error: function () {
                    Swal.fire("Error", "Failed to verify user.", "error");
                }
            });
        }
    });
}


</script>

@endpush
