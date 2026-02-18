@extends('admin.layouts.master')

@section('contents')
<style>
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
        box-shadow: 0 2px 6px rgba(0,0,0,.15);
    }
    .user-name {
        font-size: 13px;
        font-weight: 500;
    }
</style>

<div class="container-xxl container-p-y"
     x-data="areaEvaluation(@json($currentUserEvaluation ? true : false))"
     x-init="init()">

    {{-- BACK --}}
    <div class="mb-3">
        <a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-secondary">
            ← Back to Areas
        </a>
    </div>

    {{-- HEADER --}}
    <h4 class="fw-bold mb-1">{{ $programArea->area->area_name }}</h4>
    <p class="text-muted mb-4">Program Area Evaluation</p>

    {{-- LOCK / FINAL WARNING --}}
    @if($currentUserEvaluation?->is_final)
        <div class="alert alert-success d-flex justify-content-center text-center">
            <i class="bx bx-lock"></i>
            You already finalized your evaluation. Editing is locked.
        </div>
    @elseif ($isSubmittedOrUpdated)
        <div class="alert alert-warning d-flex justify-content-between align-items-center">
            <div>
                <i class="bx bx-lock"></i>
                You already evaluated this area. Click ‘Edit Evaluation’ to make changes if needed.
            </div>
            <button class="btn btn-sm btn-warning" @click="unlockForm()">
                <i class="bx bx-edit"></i> Edit Evaluation
            </button>
        </div>
    @endif

    {{-- ASSIGNED USERS --}}
    @if ($programArea->users->count() > 0)
    <div class="card mb-4">
        <div class="card-body">
            <h6 class="fw-bold mb-3">Assigned Internal Assessors</h6>
            <div class="users-grid">
                @foreach ($programArea->users as $user)
                    <div class="user-box">
                        <div class="user-avatar">
                            {{ strtoupper(substr($user->name, 0, 2)) }}
                        </div>
                        <div class="user-name">
                            {{ $user->name }}
                            @if ($user->id === auth()->id() && $user->user_type === \App\Enums\UserType::INTERNAL_ASSESSOR)
                                <span class="text-muted">(You)</span>
                            @endif
                        </div>
                        <div class="user-name text-primary">
                            {{ $user->user_type }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    {{-- AREA EVALUATION --}}
    <div class="card mb-4">
        <div class="card-body">
            <h6 class="fw-bold mb-3">Area Evaluation</h6>

            <table class="table table-bordered table-sm align-middle">
                <thead class="table-light">
                    <tr class="text-center">
                        <th style="width:30%; vertical-align: middle;" class="fw-bold">Checklist Item</th>
                        <th style="width:18%; vertical-align: top;">
                            <div class="fw-bold">Available</div>
                            <div class="small text-start mt-1">
                                <div>5 – Available and very adequate</div>
                                <div>4 – Available and adequate</div>
                                <div>3 – Available and fairly adequate</div>
                            </div>
                        </th>
                        <th style="width:18%; vertical-align: top;">
                            <div class="fw-bold">Available but Inadequate</div>
                            <div class="small text-start mt-1">
                                <div>2 – Available but inadequate</div>
                                <div>1 – Available but very inadequate</div>
                            </div>
                        </th>
                        <th style="width:14%; vertical-align: top;">
                            <div class="fw-bold">Not Available</div>
                            <div class="small mt-1">0 – No supporting document</div>
                        </th>
                        <th style="width:14%; vertical-align: top;">
                            <div class="fw-bold">Not Applicable</div>
                            <div class="small mt-1">N/A - Excluded from computation</div>
                        </th>
                        <th style="width:10%; vertical-align: middle;" class="fw-bold">Documents</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($parameters as $parameter)
                    <tr class="table-secondary fw-semibold">
                        <td colspan="6">{{ $parameter->parameter_name }}</td>
                    </tr>

                    @foreach($parameter->sub_parameters as $sub)
                    <tr>
                        <td style="padding-left: 30px">{{ $sub->sub_parameter_name }}</td>

                        <td class="text-center">
                            <select class="form-select form-select-sm"
                                    :disabled="locked"
                                    :value="getStatus('{{ $sub->id }}') === 'available' ? getScore('{{ $sub->id }}') : ''"
                                    @change="select('{{ $sub->id }}','available',$event.target.value)">
                                <option value="">—</option>
                                <option value="5">5</option>
                                <option value="4">4</option>
                                <option value="3">3</option>
                            </select>
                        </td>

                        <td class="text-center">
                            <select class="form-select form-select-sm"
                                    :disabled="locked"
                                    :value="getStatus('{{ $sub->id }}') === 'inadequate' ? getScore('{{ $sub->id }}') : ''"
                                    @change="select('{{ $sub->id }}','inadequate',$event.target.value)">
                                <option value="">—</option>
                                <option value="2">2</option>
                                <option value="1">1</option>
                            </select>
                        </td>

                        <td class="text-center">
                            <input type="radio"
                                   :disabled="locked"
                                   name="eval_{{ $sub->id }}"
                                   :checked="getStatus('{{ $sub->id }}') === 'not_available'"
                                   @change="select('{{ $sub->id }}','not_available',0)">
                        </td>

                        <td class="text-center">
                            <input type="radio"
                                   :disabled="locked"
                                   name="eval_{{ $sub->id }}"
                                   :checked="getStatus('{{ $sub->id }}') === 'not_applicable'"
                                   @change="select('{{ $sub->id }}','not_applicable','NA')">
                        </td>

                        <td class="text-center">
                            @if($sub->uploads->count() > 0)
                                <a href="{{ route('subparam.uploads.index', [
                                    'subParameter'   => $sub->id,
                                    'infoId'         => $infoId,
                                    'levelId'        => $levelId,
                                    'programId'      => $programId,
                                    'programAreaId'  => $programAreaId,
                                ]) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bx bxs-file-pdf"></i>
                                    <span>{{ $sub->uploads->count() }}</span>
                                </a>
                            @else
                                <span class="text-muted small">No available document</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                @endforeach
                </tbody>

                <tfoot class="fw-semibold">
                    <tr>
                        <td>Total</td>
                        <td class="text-center" x-text="totals.available"></td>
                        <td class="text-center" x-text="totals.inadequate"></td>
                        <td class="text-center" x-text="totals.not_available"></td>
                        <td class="text-center" x-text="totals.not_applicable"></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Area Mean</td>
                        <td colspan="5" class="text-center fs-5 fw-bold" x-text="mean"></td>
                    </tr>
                </tfoot>
            </table>

            {{-- RECOMMENDATION --}}
            <div class="mt-4">
                <label class="fw-bold">Recommendations</label>
                <textarea class="form-control"
                          rows="4"
                          :disabled="locked"
                          x-model="recommendation"
                          @input="save()"></textarea>
            </div>

            {{-- ACTION BUTTONS --}}
            <div class="mt-4 d-flex justify-content-end gap-2">
                <button type="button"
                        class="btn btn-outline-danger"
                        :disabled="locked"
                        @click="clearAll()">
                    <i class="bx bx-trash"></i> Clear All
                </button>

                <button type="button"
                        class="btn btn-primary"
                        :disabled="locked || !isComplete()"
                        @click="submitEvaluation()">
                    <i class="bx bx-send"></i> Submit Evaluation
                </button>
            </div>

            {{-- REMINDER --}}
            <div class="mt-2 text-end"
                 x-show="hasMissingEvaluations()"
                 x-transition
                 style="font-size: 13px;">
                <i class="bx bx-info-circle text-warning"></i>
                <span class="text-warning">
                    Please evaluate all checklist items before submitting.
                </span>
            </div>

        </div>
    </div>
</div>

<script>
    window.TOTAL_SUBPARAMETERS = {{ $parameters->sum(fn($p) => $p->sub_parameters->count()) }};
</script>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('areaEvaluation', (locked = false) => ({
        locked,
        accredInfoId: {{ $infoId }},
        levelId: {{ $levelId }},
        programId: {{ $programId }},
        programAreaId: {{ $programAreaId }},
        storageKey: 'area-eval-{{ auth()->id() }}-{{ $programAreaId }}-{{ $levelId }}-{{ $programId }}',

        evaluations: @json($currentUserEvaluation ? collect($currentUserEvaluation->subparameterRatings)->keyBy('subparameter_id')->map(fn($r) => [
            'status' => $r->ratingOption->label === 'Available' ? 'available' :
                       ($r->ratingOption->label === 'Available but Inadequate' ? 'inadequate' :
                       ($r->ratingOption->label === 'Not Available' ? 'not_available' : 'not_applicable')),
            'score'  => $r->score
        ]) : []),
        totals: { available: 0, inadequate: 0, not_available: 0, not_applicable: 0 },
        mean: '0.00',
        recommendation: @json($currentUserEvaluation?->areaRecommendations?->first()?->recommendation ?? ''),

        init() {
            // Check localStorage first
            const saved = localStorage.getItem(this.storageKey)
            if (saved) {
                const data = JSON.parse(saved)
                this.evaluations = data.evaluations ?? this.evaluations
                this.recommendation = data.recommendation ?? this.recommendation

                // Unlock if there is unsaved data
                if (Object.keys(this.evaluations).length > 0 || this.recommendation) {
                    this.locked = false
                }
            } else if (this.locked) {
                // No saved data, respect server-side lock
                this.locked = this.locked
            }
            this.compute()
        },

        isComplete() {
            return Object.keys(this.evaluations).length === window.TOTAL_SUBPARAMETERS
        },

        hasMissingEvaluations() {
            return Object.keys(this.evaluations).length < window.TOTAL_SUBPARAMETERS
        },

        getStatus(id) {
            return this.evaluations[id]?.status ?? null
        },

        getScore(id) {
            return this.evaluations[id]?.score ?? ''
        },

        select(subId, status, score) {
            if (this.locked) return

            if (status === 'not_applicable') {
                this.evaluations[subId] = { status, score: null }

            } else if (status === 'not_available') {
                this.evaluations[subId] = { status, score: 0 }

            } else if (score !== '') {
                this.evaluations[subId] = { status, score: parseInt(score) }

            } else {
                return // DO NOT delete — just ignore empty selection
            }

            this.compute()
            this.save()
        },

        unlockForm() {
            this.locked = false;

            // Scroll to area evaluation card
            this.$nextTick(() => {
                const form = document.querySelector('.table')
                if (form) form.scrollIntoView({ behavior: 'smooth' })
            })
        },

        compute() {
            let totalScore = 0

            this.totals = {
                available: 0,
                inadequate: 0,
                not_available: 0,
                not_applicable: 0,
            }

            Object.values(this.evaluations).forEach(item => {
                if (item.status === 'available' || item.status === 'inadequate') {
                    totalScore += item.score
                    this.totals[item.status] += item.score
                }
            })

            this.mean = window.TOTAL_SUBPARAMETERS > 0
                ? (totalScore / window.TOTAL_SUBPARAMETERS).toFixed(2)
                : '0.00'
        },

        save() {
            localStorage.setItem(this.storageKey, JSON.stringify({
                evaluations: this.evaluations,
                recommendation: this.recommendation
            }))
        },

        clearAll() {
            if (this.locked) return
            if (!confirm('Clear all evaluations?')) return
            localStorage.removeItem(this.storageKey)
            this.evaluations = {}
            this.recommendation = ''
            this.compute()
        },

        async submitEvaluation() {
            if (this.locked) return

            if (!this.isComplete()) {
                alert('Please evaluate all checklist items before submitting.')
                return
            }

            const response = await fetch('{{ route('accreditation-evaluations.store') }}', {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    accred_info_id: this.accredInfoId,
                    level_id: this.levelId,
                    program_id: this.programId,
                    program_area_id: this.programAreaId,
                    evaluations: this.evaluations,
                    recommendation: this.recommendation
                })
            })

            const data = await response.json()
            
            if (!response.ok) {
               showToast(data.message ?? 'Something went wrong.', 'error')
                return
            }

            if (!data.redirect) {
                showToast(`Missing data: ${data}`, 'error');
                return
            }

            localStorage.removeItem(this.storageKey)
            window.location.href = data.redirect
        }
    }))
})
</script>
@endsection
