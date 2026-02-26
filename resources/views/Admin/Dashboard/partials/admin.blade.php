<div class="container-fluid">

    <h2 class="mb-4 fw-bold">Admin Dashboard</h2>

    {{-- Summary Cards --}}
    <div class="row">

        <div class="col-md-3 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h6 class="text-muted">Total Assessors and Accreditors</h6>
                    <h3 class="fw-bold">128</h3>
                    <small class="text-success">+12 this month</small>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h6 class="text-muted">Ongoing Accreditations</h6>
                    <h3 class="fw-bold text-warning">3</h3>
                    <small>Visit scheduled this March</small>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h6 class="text-muted">Accreditation Programs</h6>
                    <h3 class="fw-bold">9</h3>
                    <small class="text-muted">Across 4 Colleges</small>
                </div>
            </div>
        </div>

        

        <div class="col-md-3 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h6 class="text-muted">Completed Accreditations</h6>
                    <h3 class="fw-bold text-success">6</h3>
                    <small>Last updated Jan 2026</small>
                </div>
            </div>
        </div>

    </div>

    {{-- Recent Activity --}}
    <div class="card shadow-sm border-0 mt-4">
        <div class="card-header bg-white fw-semibold">
            Recent Activities
        </div>
        <div class="card-body">

            <ul class="list-group list-group-flush">
                <li class="list-group-item">
                    ✔ Internal Assessor assigned to Area II.
                </li>
                <li class="list-group-item">
                    ✔ Task Force submitted Area I evaluation.
                </li>
                <li class="list-group-item">
                    ✔ New Accreditation created for BSIT.
                </li>
                <li class="list-group-item">
                    ✔ Dean updated program details.
                </li>
            </ul>

        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="card shadow-sm border-0 mt-4">
        <div class="card-header bg-white fw-semibold">
            Quick Actions
        </div>
        <div class="card-body">

            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary me-2">
                View Pending Accounts
            </a>

            <a href="{{ route('users.taskforce.index') }}" class="btn btn-primary me-2">
                View Active Accounts
            </a>

            <a href="{{ route('program.areas.evaluations') }}" class="btn btn-outline-success">
                View Assessor's Evaluations
            </a>

        </div>
    </div>

</div>
