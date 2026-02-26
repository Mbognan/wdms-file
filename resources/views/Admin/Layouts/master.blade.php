
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
    <link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}">

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css') }}" />


    <!-- Helpers -->
    <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>
    <script src="{{ asset('assets/js/config.js') }}"></script>
    <style>
        .swal2-container {
            z-index: 99999 !important;
        }


        .swal2-backdrop-show {
            background: rgba(0, 0, 0, 0.6) !important;
        }

        .avatar {
            flex-shrink: 0;
            aspect-ratio: 1/1; /* forces perfect square */
        }

        #layout-menu .menu-item.active > .menu-link {
            background-color: rgba(0,0,0,0.2);
        }

        #layout-menu .menu-item:hover > .menu-link {
            background-color: rgba(0,0,0,0.1);
        }

        #layout-menu .menu-sub .menu-item > .menu-link {
            padding-left: 2rem;
        }

        #layout-menu .menu-item .badge {
            font-size: 0.7rem;
        } 
        </style>
</head>

<body>

<!-- Layout wrapper -->
<div class="layout-wrapper layout-content-navbar" id="app">
    <div class="layout-container" id="vue-app">
        <div class="layout-page">

            <!-- Navbar -->
             <nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
                    id="layout-navbar">
                    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
                        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)" @click.prevent="toggleSidebar">
                            <i class="bx bx-menu bx-sm"></i>
                        </a>
                    </div>

                    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
                        <div class="navbar-nav align-items-center">
                            <div class="nav-item d-flex align-items-center">
                                WEB-BASED ACCREDITATION DOCUMENT MANAGEMENT SYSTEM
                            </div>
                        </div>

                        <div class="navbar-nav align-items-center ms-auto">
                            <div class="nav-item d-flex align-items-center">
                                <button type="button" class="btn btn-icon btn-primary" @click="$refs.globalSearch.open()">
                                    <i class="bx bx-search"></i>
                                </button>
                            </div>
                        </div>
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

                <div class="content-backdrop fade"></div>
            </div>
            <!-- / Content wrapper -->
        </div>
        <!-- Global search modal controlled by Vue -->
        <global-search ref="globalSearch" :user-role="'{{ $user->currentRole->name }}'"></global-search>
    </div>
    <!-- Global toast container -->
    
    <div class="layout-overlay layout-menu-toggle"></div>
     @include('admin.layouts.sidebar')
</div>
<!-- Core JS -->
<script src="{{ asset('assets/js/vue.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
<script src="{{ asset('assets/js/axios.js') }}"></script>
<script src="{{ asset('assets/js/select2.min.js') }}"></script>

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
<script async defer src="{{ asset('assets/js/buttons.js') }}"></script>
<script src="{{ asset('assets/js/sweetalert.js') }}"></script>
<script src="{{ asset('assets/js/alpine.js') }}" defer></script>
<script>
    Vue.component('global-search', {
    props: {
        userRole: {
            type: String,
            required: true
        }
    },
    data() {
        return {
            query: '',
            results: [],
            loading: false,
            modalInstance: null,
            searchTimeout: null
        };
    },
    computed: {
        placeholderText() {
            switch(this.userRole) {
                @php
                    use App\Enums\UserType;
                @endphp
                case '{{ UserType::ADMIN->value }}':
                    return 'Search internal assessors, accreditors, programs, areas, parameters, subparameters, documents, evaluations...';
                case '{{ UserType::DEAN->value }}':
                    return 'Search task forces, programs, areas, parameters, subparameters, documents, evaluations, archive...';
                case '{{ UserType::TASK_FORCE->value }}':
                case '{{ UserType::INTERNAL_ASSESSOR->value }}':
                    return 'Search programs, areas, parameters, subparameters, documents, evaluations (assigned areas only)...';
                case '{{ UserType::ACCREDITOR->value }}':
                    return 'Search programs, areas, parameters, subparameters, documents, evaluations...';
                default:
                    return 'Type to search...';
            }
        }
    },
    mounted() {
        // Modal stays open on backdrop click and Esc key
        this.modalInstance = new bootstrap.Modal(this.$refs.modal, {
            backdrop: 'static',
            keyboard: false
        });
    },
    methods: {
        open() {
            this.query = '';
            this.results = [];
            this.modalInstance.show();
            this.$nextTick(() => {
                this.$refs.input.focus();
            });
        },
        close() {
            this.modalInstance.hide();
        },
        debouncedSearch() {
            if (this.searchTimeout) clearTimeout(this.searchTimeout);
            if (this.query.length < 1) {
                this.results = [];
                return;
            }
            this.searchTimeout = setTimeout(() => {
                this.performSearch();
            }, 300);
        },
        performSearch() {
            this.loading = true;
            axios.get('{{ route('global.search') }}', { params: { q: this.query } })
                .then(response => {
                    console.log(response.data);
                    this.results = response.data;
                })
                .catch(error => {
                    console.error('Search error:', error);
                    this.results = [];
                })
                .finally(() => {
                    this.loading = false;
                });
        },
        highlight(text) {
            if (!this.query) return text;
            const regex = new RegExp(`(${this.query})`, 'gi');
            return text.replace(regex, '<mark>$1</mark>');
        }
    },
    template: `
        <div class="modal fade" ref="modal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-top">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title fw-bold">Global Search</h3>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <input type="text"
                                ref="input"
                                class="form-control form-control-lg"
                                :placeholder="placeholderText"
                                v-model="query"
                                @input="debouncedSearch"
                                autocomplete="off">
                        </div>
                        <div class="list-group" style="max-height: 400px; overflow-y: auto;">

                            <div v-if="loading" class="text-center py-3">
                                <span class="spinner-border spinner-border-sm me-2"></span>Searching...
                            </div>
                            <div v-else-if="results.length === 0 && query.length > 0"
                                class="text-muted text-center py-3">
                                No results found.
                            </div>
                            <div v-else-if="query.length === 0"
                                class="text-muted text-center py-3">
                                Start typing to search...
                            </div>

                            <a v-for="item in results"
                                :key="item.id"
                                :href="item.url"
                                class="list-group-item list-group-item-action d-flex align-items-start gap-3 py-2">

                                <!-- Icon -->
                                <i :class="'bx ' + item.icon + ' bx-sm mt-1 text-muted'"></i>

                                <!-- Content -->
                                <div class="flex-grow-1" style="min-width:0;">

                                    <!-- Title + Badge -->
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        <span class="fw-semibold" v-html="highlight(item.title)"></span>
                                        <span v-if="item.badge"
                                            :class="'badge bg-label-' + item.badge_color"
                                            style="font-size:0.7rem; white-space:nowrap;">
                                            @{{ item.badge }}
                                        </span>
                                    </div>

                                    <!-- Subtitle (always shown) -->
                                    <small class="text-muted d-block text-truncate" v-html="highlight(item.subtitle)"></small>

                                    <!-- ── USER meta ── -->
                                    <template v-if="item.type === 'user'">
                                        <small v-if="item.meta.areas_count > 0" class="text-muted d-block text-truncate">
                                            <i class="bx bx-layer bx-xs"></i>
                                            Assigned to @{{ item.meta.areas_count }} area(s): @{{ item.meta.areas.map(a => a.name).join(', ') }}
                                        </small>
                                        <small v-if="item.meta.status" class="text-muted d-block">
                                            <i class="bx bx-circle bx-xs"></i>
                                            @{{ item.meta.status }}
                                            <span v-if="item.meta.created_at"> · Joined @{{ item.meta.created_at }}</span>
                                        </small>
                                    </template>

                                    <!-- ── ACCREDITATION meta ── -->
                                    <template v-else-if="item.type === 'accreditation'">
                                        <small class="text-muted d-block">
                                            <i class="bx bx-buildings bx-xs"></i>
                                            @{{ item.meta.body.name ?? 'No Body' }}
                                            <span v-if="item.meta.date"> · @{{ item.meta.date }}</span>
                                            <span v-if="item.meta.visit_type"> · @{{ item.meta.visit_type }}</span>
                                        </small>
                                        <small v-if="item.meta.levels_count > 0" class="text-muted d-block text-truncate">
                                            <i class="bx bx-bar-chart-alt-2 bx-xs"></i>
                                            @{{ item.meta.levels_count }} level(s):
                                            @{{ item.meta.levels.map(l => l.name).join(', ') }}
                                            · @{{ item.meta.programs_count }} program(s)
                                        </small>
                                    </template>

                                    <!-- ── PROGRAM meta ── -->
                                    <template v-else-if="item.type === 'program'">
                                        <small class="text-muted d-block">
                                            <i class="bx bx-certification bx-xs"></i>
                                            @{{ item.meta.accreditation.name ?? 'Unknown Accreditation' }}
                                            <span v-if="item.meta.accreditation.year"> · @{{ item.meta.accreditation.year }}</span>
                                            <span v-if="item.meta.accreditation.body"> · @{{ item.meta.accreditation.body }}</span>
                                        </small>
                                        <small class="text-muted d-block text-truncate">
                                            <i class="bx bx-layer bx-xs"></i>
                                            @{{ item.meta.areas_count }} area(s)
                                            <span v-if="item.meta.areas.length > 0">: @{{ item.meta.areas.map(a => a.name).join(', ') }}</span>
                                        </small>
                                    </template>

                                    <!-- ── AREA meta ── -->
                                    <template v-else-if="item.type === 'area'">
                                        <small class="text-muted d-block">
                                            <i class="bx bx-certification bx-xs"></i>
                                            @{{ item.meta.accreditation.name ?? 'Unknown Accreditation' }}
                                            <span v-if="item.meta.accreditation.year"> · @{{ item.meta.accreditation.year }}</span>
                                        </small>
                                        <small class="text-muted d-block">
                                            <i class="bx bx-book bx-xs"></i>
                                            @{{ item.meta.program.name ?? 'Unknown Program' }}
                                            <span v-if="item.meta.level.name">
                                                · <i class="bx bx-bar-chart-alt-2 bx-xs"></i> @{{ item.meta.level.name }}
                                            </span>
                                        </small>
                                        <small v-if="item.meta.description" class="text-muted d-block text-truncate">
                                            <i class="bx bx-info-circle bx-xs"></i>
                                            @{{ item.meta.description }}
                                        </small>
                                    </template>

                                    <!-- ── PARAMETER meta ── -->
                                    <template v-else-if="item.type === 'parameter'">
                                        <small class="text-muted d-block">
                                            <i class="bx bx-layer bx-xs"></i>
                                            @{{ item.meta.area.name ?? 'Unknown Area' }}
                                            · <i class="bx bx-book bx-xs"></i> @{{ item.meta.program.name ?? 'Unknown Program' }}
                                            · <i class="bx bx-bar-chart-alt-2 bx-xs"></i> @{{ item.meta.level.name ?? 'Unknown Level' }}
                                        </small>
                                        <small class="text-muted d-block">
                                            <i class="bx bx-certification bx-xs"></i>
                                            @{{ item.meta.accreditation.name ?? 'Unknown Accreditation' }}
                                            <span v-if="item.meta.accreditation.year"> · @{{ item.meta.accreditation.year }}</span>
                                        </small>
                                        <small v-if="item.meta.sub_parameters_count > 0" class="text-muted d-block text-truncate">
                                            <i class="bx bx-subdirectory-right bx-xs"></i>
                                            @{{ item.meta.sub_parameters_count }} sub-parameter(s):
                                            @{{ item.meta.sub_parameters.map(s => s.name).join(', ') }}
                                        </small>
                                    </template>

                                    <!-- ── SUB-PARAMETER meta ── -->
                                    <template v-else-if="item.type === 'sub_parameter'">
                                        <small class="text-muted d-block">
                                            <i class="bx bx-list-ul bx-xs"></i>
                                            @{{ item.meta.parameter.name ?? 'Unknown Parameter' }}
                                            · <i class="bx bx-layer bx-xs"></i> @{{ item.meta.area.name ?? 'Unknown Area' }}
                                            · <i class="bx bx-book bx-xs"></i> @{{ item.meta.program.name ?? 'Unknown Program' }}
                                        </small>
                                        <small class="text-muted d-block">
                                            <i class="bx bx-bar-chart-alt-2 bx-xs"></i>
                                            @{{ item.meta.level.name ?? 'Unknown Level' }}
                                            · <i class="bx bx-certification bx-xs"></i>
                                            @{{ item.meta.accreditation.name ?? 'Unknown Accreditation' }}
                                            <span v-if="item.meta.accreditation.year"> · @{{ item.meta.accreditation.year }}</span>
                                        </small>
                                    </template>

                                    <!-- ── FALLBACK ── -->
                                    <template v-else>
                                        <small v-if="item.subtitle" class="text-muted d-block text-truncate">
                                            @{{ item.subtitle }}
                                        </small>
                                    </template>

                                </div>
                            </a>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" @click="close">Close</button>
                    </div>
                </div>
            </div>
        </div>
    `
});

new Vue({
    el: '#vue-app',
    methods: {
        toggleSidebar() {
            document.body.classList.toggle('layout-menu-expanded');
        }
    }
});
</script>
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

$(document).on('click', '.btn-terminate', function () {

    const button = $(this);
    const userId = button.data('id');
    const url = button.data('url');

    Swal.fire({
        title: "Are you sure?",
        text: "This user will be terminated and cannot access the system!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Yes, terminate user"
    }).then((result) => {

        if (!result.isConfirmed) return;

        $.ajax({
            url: url,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function () {
                button.prop('disabled', true);
            },
            success: function (res) {

                // Reload DataTable
                if ($.fn.DataTable.isDataTable('#taskforce-table')) {
                    $('#taskforce-table').DataTable().ajax.reload(null, false);
                }

                Swal.fire({
                    icon: "success",
                    title: "Terminated!",
                    text: res.message ?? "User terminated successfully",
                    timer: 2000,
                    showConfirmButton: false
                });
            },
            error: function () {
                Swal.fire(
                    "Error",
                    "Failed to terminate user.",
                    "error"
                );
            },
            complete: function () {
                button.prop('disabled', false);
            }
        });
    });
});



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

<script>
$(document).on('click', '.btn-terminate', function () {

    const button = $(this);
    const userId = button.data('id');
    const url = button.data('url');

    Swal.fire({
        title: "Are you sure?",
        text: "This user will be terminated and cannot access the system!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Yes, terminate user"
    }).then((result) => {

        if (!result.isConfirmed) return;

        $.ajax({
            url: url,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function () {
                button.prop('disabled', true);
            },
            success: function (res) {

                // Reload DataTable
                if ($.fn.DataTable.isDataTable('#taskforce-table')) {
                    $('#taskforce-table').DataTable().ajax.reload(null, false);
                }

                Swal.fire({
                    icon: "success",
                    title: "Terminated!",
                    text: res.message ?? "User terminated successfully",
                    timer: 2000,
                    showConfirmButton: false
                });
            },
            error: function () {
                Swal.fire(
                    "Error",
                    "Failed to terminate user.",
                    "error"
                );
            },
            complete: function () {
                button.prop('disabled', false);
            }
        });
    });
});
</script>


@stack('scripts')

<div class="toast-container position-fixed top-0 end-0 p-3" id="globalToastContainer"  style="z-index: 2000;"></div>
</body>
</html>

