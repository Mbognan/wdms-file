
<!DOCTYPE html>

<html lang="en"
      class="light-style layout-menu-fixed layout-compact"
      dir="ltr"
      data-theme="theme-default"
      data-assets-path="{{ asset('assets/') }}"
      data-template="vertical-menu-template-free">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, user-scalable=no,
                   minimum-scale=1.0, maximum-scale=1.0" />

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title></title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap"
          rel="stylesheet" />

    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/boxicons.css') }}" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/theme-default.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css') }}" />

    <!-- Helpers -->
    <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>
    <script src="{{ asset('assets/js/config.js') }}"></script>
</head>

<body>

<!-- Layout wrapper -->
<div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
        <div class="layout-page">

            @include('admin.layouts.sidebar')

            <!-- Navbar -->
             <nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
                    id="layout-navbar">
                    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
                        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                            <i class="bx bx-menu bx-sm"></i>
                        </a>
                    </div>

                    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
                        <!-- Search -->
                        <div class="navbar-nav align-items-center">
                            <div class="nav-item d-flex align-items-center">
                                WEB-BASED ACCREDITATION DOCUMENT MANAGEMENT SYSTEM
                            </div>
                        </div>

                        <ul class="navbar-nav flex-row align-items-center ms-auto">
                            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                                    @php $user = auth()->user(); @endphp
                                    <div class="avatar avatar-online">
                                        @if ($user->profile_photo)
                                            <img
                                                src="{{ asset('storage/' . $user->profile_photo) }}"
                                                alt="Avatar"
                                                class="w-px-40 h-px-40 rounded-circle object-fit-cover border border-primary"
                                            />
                                        @else
                                            <img
                                                src="{{ asset('assets/img/avatars/1.png') }}"
                                                alt="Default Avatar"
                                                class="w-px-40 h-px-40 rounded-circle object-fit-cover border border-2 border-primary"
                                            />
                                        @endif
                                    </div>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <!-- User Info -->
                                    <li>
                                        <a class="dropdown-item" href="#">
                                            <div class="d-flex">
                                                <div class="flex-shrink-0 me-3">
                                                    <div class="avatar avatar-online">
                                                        <img src="{{ asset('assets/img/avatars/1.png') }}" alt class="w-px-40 h-auto rounded-circle" />
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <span class="fw-medium d-block">{{ auth()->user()->name }}</span>
                                                    <small class="text-muted">{{ auth()->user()->user_type }}</small>
                                                </div>
                                            </div>
                                        </a>
                                    </li>

                                    <li>
                                        <div class="dropdown-divider"></div>
                                    </li>

                                    <!-- My Profile -->
                                    <li>
                                        <a class="dropdown-item" href="#">
                                            <i class="bx bx-user me-2"></i>
                                            <span class="align-middle">My Profile</span>
                                        </a>
                                    </li>

                                    <li>
                                        <div class="dropdown-divider"></div>
                                    </li>

                                    <!-- Log Out -->
                                    <li>
                                        <form method="POST" action="{{ route('logout') }}" id="logout-form">
                                            @csrf
                                            <a class="dropdown-item" href="#"
                                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                                <i class="bx bx-power-off me-2"></i>
                                                <span class="align-middle">Log Out</span>
                                            </a>
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        </ul>

                    </div>
                </nav>

            <!-- Content wrapper -->
            <div class="content-wrapper">

                {{-- GLOBAL TOAST CONTAINER --}}
                <div class="toast-container position-fixed top-0 end-0 p-3"></div>

                {{-- GLOBAL TOAST TEMPLATE --}}
                <div id="appToast"
                     class="toast align-items-center text-white border-0"
                     role="alert"
                     aria-live="assertive"
                     aria-atomic="true">
                    <div class="d-flex">
                        <div class="toast-body" id="toastMessage"></div>
                        <button type="button"
                                class="btn-close btn-close-white me-2 m-auto"
                                data-bs-dismiss="toast"
                                aria-label="Close"></button>
                    </div>
                </div>

                @yield('contents')

                @include('admin.layouts.footer')

                <div class="content-backdrop fade"></div>
            </div>
            <!-- / Content wrapper -->
        </div>
    </div>
    <!-- Global toast container -->

    <div class="layout-overlay layout-menu-toggle"></div>



</div>
 <div class="toast-container position-fixed top-0 end-0 p-3" id="globalToastContainer"  style="z-index: 2000;"></div>
<!-- Core JS -->
<script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<link rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css">
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>

<script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
<script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
<script src="{{ asset('assets/vendor/js/menu.js') }}"></script>

<!-- Vendors JS -->
<script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>

<!-- Main JS -->
<script src="{{ asset('assets/js/main.js') }}"></script>

<!-- Page JS -->
<script src="{{ asset('assets/js/dashboards-analytics.js') }}"></script>

<!-- GitHub buttons -->
<script async defer src="https://buttons.github.io/buttons.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

<script>
function showToast(message, type = 'success') {
    const container = document.getElementById('globalToastContainer');

    const toastEl = document.createElement('div');
    toastEl.className = 'toast align-items-center text-white border-0 fade';
    toastEl.setAttribute('role', 'alert');
    toastEl.setAttribute('aria-live', 'assertive');
    toastEl.setAttribute('aria-atomic', 'true');

    switch(type) {
        case 'success': toastEl.classList.add('bg-success'); break;
        case 'error': toastEl.classList.add('bg-danger'); break;
        case 'warning': toastEl.classList.add('bg-warning'); break;
        case 'info': toastEl.classList.add('bg-info'); break;
        default: toastEl.classList.add('bg-primary');
    }

    toastEl.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">${message}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    `;

    container.appendChild(toastEl);

    const toast = new bootstrap.Toast(toastEl, { delay: 3000 });
    toast.show();

    toastEl.addEventListener('hidden.bs.toast', () => {
        toastEl.remove();
    });
}

// $(document).on('click', '.btn-suspend', function () {

//     const button = $(this);
//     const userId = button.data('id');
//     const url = button.data('url');

//     Swal.fire({
//         title: "Are you sure?",
//         text: "You won't be able to revert this!",
//         icon: "warning",
//         showCancelButton: true,
//         confirmButtonColor: "#3085d6",
//         cancelButtonColor: "#d33",
//         confirmButtonText: "Yes, suspend it!"
//     }).then((result) => {

//         if (!result.isConfirmed) return;

//         $.ajax({
//             url: url,
//             type: 'DELETE',
//             headers: {
//                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
//             },
//             beforeSend: function () {
//                 button.prop('disabled', true);
//             },
//             success: function (res) {

//                 // Reload DataTable if exists
//                 if ($.fn.DataTable.isDataTable('#users-table')) {
//                     $('#users-table').DataTable().ajax.reload(null, false);
//                 }

//                 Swal.fire({
//                     title: "Suspended!",
//                     text: res.message ?? "User suspended successfully.",
//                     icon: "success",
//                     timer: 2000,
//                     showConfirmButton: false
//                 });
//             },
//             error: function () {
//                 Swal.fire(
//                     "Error",
//                     "Failed to suspend user.",
//                     "error"
//                 );
//             },
//             complete: function () {
//                 button.prop('disabled', false);
//             }
//         });
//     });
// });
</script>

@if(session('success'))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        showToast("{{ session('success') }}", "success");
    });
</script>
@endif

@if(session('error'))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        showToast("{{ session('error') }}", "error");
    });
</script>
@endif



@stack('scripts')

</body>
</html>

