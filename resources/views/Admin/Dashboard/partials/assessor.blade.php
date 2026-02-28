<div class="container-fluid">

    <h2 class="mb-1 fw-bold d-flex align-items-center justify-content-between">
        Internal Assessor Dashboard
        <button class="btn btn-sm btn-outline-secondary" onclick="refreshDashboard()" id="refreshBtn">
            <i class="bx bx-refresh me-1"></i> Refresh
        </button>
    </h2>
    <p class="text-muted mb-4">Overview of your assigned areas and evaluation progress.</p>

    {{-- ── STAT CARDS ── --}}
    <div class="row g-3 mb-2">

        <div class="col-6 col-md-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-3 p-2 bg-success bg-opacity-10 text-primary fs-4 d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                        <i class="bx bx-layer"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-0" style="font-size:.75rem;">Assigned {{ $totalAssignedAreas > 1 ? 'Areas' : 'Area' }}</p>
                        <h4 class="fw-bold mb-0">{{ $totalAssignedAreas }}</h4>
                        <small class="text-muted">Your assignments</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-3 p-2 bg-warning bg-opacity-10 text-warning fs-4 d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                        <i class="bx bx-send"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-0" style="font-size:.75rem;">Submitted Evaluations</p>
                        <h4 class="fw-bold mb-0 text-warning">{{ $submittedEvaluations }}</h4>
                        <small class="text-muted">Your submissions</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-3 p-2 bg-success bg-opacity-10 text-success fs-4 d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                        <i class="bx bx-badge-check"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-0" style="font-size:.75rem;">Finalized Evaluations</p>
                        <h4 class="fw-bold mb-0 text-success">{{ $finalizedEvaluations }}</h4>
                        <small class="text-muted">Your finalizations</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-3 p-2 bg-info bg-opacity-10 text-info fs-4 d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                        <i class="bx bx-file"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-0" style="font-size:.75rem;">Documents Uploaded</p>
                        <h4 class="fw-bold mb-0">{{ $totalDocuments }}</h4>
                        <small class="text-muted">By task force in your areas</small>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- ── ASSIGNED AREAS OVERVIEW ── --}}
    <div class="card shadow-sm border-0 mt-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <span class="fw-semibold">Your Assigned {{ $totalAssignedAreas > 1 ? 'Areas' : 'Area' }}</span>
            @if($assignedAreas->isNotEmpty())
                @php $first = $assignedAreas->first(); @endphp
                <a href="{{ route('admin.accreditations.program', [
                    'infoId'      => $first['info_id'],
                    'levelId'     => $first['level_id'],
                    'programName' => $first['program_name'],
                ]) }}" class="btn btn-sm btn-outline-primary">
                    View Program
                </a>
            @endif
        </div>
        <div class="card-body">
            @php use App\Enums\EvaluationStatus; @endphp
            @if($assignedAreas->isNotEmpty())
                <div class="row g-2">
                    @foreach($assignedAreas as $item)
                        @php
                            [$badgeClass, $badgeLabel] = match(true) {
                                $item['status'] === EvaluationStatus::FINALIZED => ['bg-success', 'Finalized'],
                                $item['status'] === EvaluationStatus::SUBMITTED => ['bg-primary', 'Submitted'],
                                $item['status'] === EvaluationStatus::UPDATED   => ['bg-warning text-dark', 'Updated'],
                                $item['evaluation'] !== null                    => ['bg-secondary', 'In Progress'],
                                default                                         => ['bg-secondary', 'Not Started'],
                            };

                            $href = $item['evaluation']
                                ? route('program.areas.evaluation', [
                                    'infoId'        => $item['info_id'],
                                    'levelId'       => $item['level_id'],
                                    'programId'     => $item['program_id'],
                                    'programAreaId' => $item['area_id'],
                                ])
                                : route('program.areas.parameters', [
                                    'infoId'        => $item['info_id'],
                                    'levelId'       => $item['level_id'],
                                    'programId'     => $item['program_id'],
                                    'programAreaId' => $item['area_id'],
                                ]);
                        @endphp
                        <div class="col-12 col-md-6 col-xl-4">
                            <a href="{{ $href }}" class="text-decoration-none">
                                <div class="card border h-100 shadow-none"
                                     onmouseover="this.style.boxShadow='0 2px 8px rgba(0,0,0,.08)'; this.style.borderColor='#0d6efd';"
                                     onmouseout="this.style.boxShadow='none'; this.style.borderColor='#dee2e6';"
                                     style="transition: box-shadow .15s, border-color .15s;">
                                    <div class="card-body py-2 px-3 d-flex justify-content-between align-items-center gap-2">
                                        <div style="min-width:0;">
                                            <p class="fw-semibold mb-0 text-truncate text-dark" style="font-size:.83rem;" title="{{ $item['area_name'] }}">
                                                {{ $item['area_name'] }}
                                            </p>
                                            <small class="text-muted d-block">
                                                <i class="bx bx-book-open me-1"></i>{{ $item['info_title'] }}
                                            </small>
                                            <small class="text-muted d-block">
                                                <i class="bx bx-layer me-1"></i>{{ $item['level_name'] }} · {{ $item['program_name'] }}
                                            </small>
                                        </div>
                                        <span class="badge {{ $badgeClass }} flex-shrink-0">{{ $badgeLabel }}</span>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-muted mb-0">No areas assigned yet.</p>
            @endif
        </div>
    </div>

    {{-- ── BOTTOM ROW: Activity + Quick Actions ── --}}
    <div class="row g-3 mt-2">

        {{-- Recent Activity --}}
        <div class="col-lg-7">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white fw-semibold">Recent Activities</div>
                <div class="card-body p-0" style="max-height:400px; overflow-y:auto;">
                    <ul class="list-group list-group-flush">
                        @forelse($recentActivities as $act)
                            <li class="list-group-item d-flex align-items-center gap-3 py-3">
                                <i class="bx {{ $act['icon'] }} {{ $act['color'] }} fs-5"></i>
                                <span class="flex-grow-1" style="font-size:.875rem;">{{ $act['text'] }}</span>
                                <small class="text-muted flex-shrink-0">{{ $act['time'] }}</small>
                            </li>
                        @empty
                            <li class="list-group-item text-muted py-3">No recent activity.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="col-lg-5">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white fw-semibold">Quick Actions</div>
                <div class="card-body d-flex flex-column gap-2">

                    @if($assignedAreas->isNotEmpty())
                        @php $first = $assignedAreas->first(); @endphp
                        <a href="{{ route('admin.accreditations.program', [
                            'infoId'      => $first['info_id'],
                            'levelId'     => $first['level_id'],
                            'programName' => $first['program_name'],
                        ]) }}" class="btn btn-primary text-start d-flex align-items-center gap-2">
                            <i class="bx bx-layer fs-5"></i> View Areas
                        </a>
                    @else
                        <button class="btn btn-primary text-start d-flex align-items-center gap-2" disabled>
                            <i class="bx bx-map-pin fs-5"></i> View Areas
                        </button>
                    @endif

                    <a href="{{ route('program.areas.evaluations') }}" class="btn btn-outline-success text-start d-flex align-items-center gap-2">
                        <i class="bx bx-list-check fs-5"></i> View My Evaluations
                    </a>

                </div>
            </div>
        </div>

    </div>

</div>

@push('scripts')
<script>
function refreshDashboard() {
    const btn = document.getElementById('refreshBtn');
    btn.disabled = true;
    btn.querySelector('i').classList.add('bx-spin');
    location.reload();
}
</script>
@endpush