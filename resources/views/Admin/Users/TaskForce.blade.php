@extends('admin.layouts.master')

@section('contents')

{{-- DataTables CSS --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fomantic-ui/2.9.2/semantic.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/2.3.5/css/dataTables.semanticui.css">

<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container-xxl flex-grow-1 container-p-y bg-footer-theme">
    <div class="card">
        <div class="card-header">
            <h5>Task Force Users</h5>
        </div>

        <div class="card-body">
            <table id="taskforce-table" class="table table-bordered table-striped w-100">
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

<script>
$(document).ready(function () {

    $('#taskforce-table').DataTable({
        processing: true,
        ajax: "{{ route('taskforce.data') }}",
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
                    if (status === 'Active') {
                        return '<span class="badge bg-success">Active</span>';
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

            // ACTION DROPDOWN
            {
                data: null,
                orderable: false,
                searchable: false,
                className: 'text-center align-middle',
                render: function (data, type, row) {

                    return `
                        <div class="dropdown">
                            <button class="btn p-0"
                                type="button"
                                data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <i class="bx bx-dots-vertical-rounded"></i>
                            </button>

                            <div class="dropdown-menu dropdown-menu-end">
                                <a class="dropdown-item"
                                   href="javascript:void(0);"
                                   onclick="viewUser(${row.id})">
                                    <i class="bx bx-show me-1"></i> View User
                                </a>

                                <a class="dropdown-item"
                                   href="javascript:void(0);"
                                   onclick="assignUser(${row.id})">
                                    <i class="bx bx-user-plus me-1"></i> Assign User
                                </a>

                                <a class="dropdown-item"
                                   href="javascript:void(0);"
                                   onclick="updatePosition(${row.id})">
                                    <i class="bx bx-edit me-1"></i> Update Position
                                </a>

                                <div class="dropdown-divider"></div>

                                <a class="dropdown-item text-danger btn-terminate"
   href="javascript:void(0);"
   data-id="${row.id}"
   data-url="{{ url('/users') }}/${row.id}/suspend">
    <i class="bx bx-trash me-1"></i> Terminate User
</a>

                            </div>
                        </div>
                    `;
                }
            }
        ]
    });

});


function viewUser(id) {
    window.location.href = "{{ url('taskforce/view') }}/" + id;
}

function assignUser(id) {
    console.log('Assign user', id);
}

function updatePosition(id) {
    console.log('Update position', id);
}

function terminateUser(id) {
    console.log('Terminate user', id);
}

</script>

@endpush
