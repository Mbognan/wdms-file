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

<div id="app" class="container-xxl container-p-y">
    {{-- BACK --}}
    <div class="mb-3">
        <a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-secondary">
            ← Back to Areas
        </a>
    </div>

    {{-- HEADER --}}
    <h4 class="fw-bold mb-1">{{ $programArea->area->area_name }}</h4>
    <p class="text-muted mb-4">Program Area Evaluation</p>

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
                            @if ($user->id === auth()->id() && $user->currentRole->name === \App\Enums\UserType::INTERNAL_ASSESSOR->value)
                                <span class="text-muted">(You)</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @else
    <div class="card mb-4">
        <div class="card-body text-muted">
            No internal assessors assigned to this area yet.
        </div>
    </div>
    @endif

    {{-- AREA EVALUATION --}}
    <div class="card mb-4">
        <div class="card-body">
            <h6 class="fw-bold mb-3">Area Evaluation</h6>

            {{-- Vue component --}}
            <area-evaluation
                :accred-info-id="{{ $infoId }}"
                :level-id="{{ $levelId }}"
                :program-id="{{ $programId }}"
                :program-area-id="{{ $programAreaId }}"
                :parameters="{{ json_encode($parametersArray) }}"
                :initial-evaluations="{{ json_encode($initialEvaluations) }}"
                :initial-recommendation="{{ json_encode($initialRecommendation) }}"
                :readonly="{{ $readonly ? 'true' : 'false' }}"
                :is-submitted="{{ $isSubmitted ? 'true' : 'false' }}"
                :is-finalized="{{ $isFinalized ? 'true' : 'false' }}"
                storage-key="area-eval-{{ auth()->id() }}-{{ $programAreaId }}-{{ $levelId }}-{{ $programId }}"
                submit-url="{{ route('accreditation-evaluations.store') }}"
                csrf-token="{{ csrf_token() }}"
            ></area-evaluation>
        </div>
    </div>
</div>

{{-- Vue and Component --}}
<script src="{{ asset('assets/js/vue.js') }}"></script>
<script>
Vue.component('area-evaluation', {
    props: {
        accredInfoId: Number,
        levelId: Number,
        programId: Number,
        programAreaId: Number,
        parameters: Array,
        initialEvaluations: Object,
        initialRecommendation: String,
        readonly: Boolean,
        isSubmitted: Boolean,
        isFinalized: Boolean,
        storageKey: String,
        submitUrl: String,
        csrfToken: String
    },
    data() {
        return {
            evaluations: {},
            recommendation: '',
            isLocked: this.readonly,
            saving: false,
            scrollPosition: 0
        };
    },
    computed: {
        totalSubparameters() {
            return this.parameters.reduce((acc, p) => acc + p.sub_parameters.length, 0);
        },
        isComplete() {
            return Object.keys(this.evaluations).length === this.totalSubparameters;
        },
        totals() {
            let available = 0, inadequate = 0, notAvailable = 0, notApplicable = 0;
            Object.values(this.evaluations).forEach(item => {
                if (item.status === 'available') available += item.score;
                else if (item.status === 'inadequate') inadequate += item.score;
                else if (item.status === 'not_available') notAvailable++;
                else if (item.status === 'not_applicable') notApplicable++;
            });
            return { available, inadequate, notAvailable, notApplicable };
        },
        mean() {
            let totalScore = 0;
            Object.values(this.evaluations).forEach(item => {
                if (item.status === 'available' || item.status === 'inadequate') {
                    totalScore += item.score;
                }
            });
            return this.totalSubparameters > 0
                ? (totalScore / this.totalSubparameters).toFixed(2)
                : '0.00';
        }
    },
    watch: {
        evaluations: {
            handler() { this.saveToLocalStorage(); },
            deep: true
        },
        recommendation() {
            this.saveToLocalStorage();
        }
    },
    mounted() {
        this.loadData();
        window.addEventListener('beforeunload', this.saveScrollPosition);
    },
    beforeDestroy() {
        window.removeEventListener('beforeunload', this.saveScrollPosition);
    },
    methods: {
        loadData() {
            const saved = localStorage.getItem(this.storageKey);
            if (saved) {
                try {
                    const data = JSON.parse(saved);
                    this.evaluations = data.evaluations || {};
                    this.recommendation = data.recommendation || '';
                    if (!this.isFinalized) {
                        this.isLocked = data.isLocked ?? this.readonly;
                    } else {
                        this.isLocked = true;
                    }
                } catch (e) {
                    console.warn('Failed to restore from localStorage', e);
                    this.loadInitial();
                }
            } else {
                this.loadInitial();
            }

            // Restore scroll position after data is loaded and DOM updated
            this.$nextTick(() => {
                const savedScroll = localStorage.getItem(this.storageKey + '_scroll');
                if (savedScroll) {
                    window.scrollTo(0, parseInt(savedScroll));
                }
            });
        },
        unlockForm() {
            if (this.isSubmitted) {
                this.isLocked = false;
                this.saveToLocalStorage();
                this.$nextTick(() => {
                    document.querySelector('.table')?.scrollIntoView({ behavior: 'smooth' });
                });
            }
        },
        loadInitial() {
            this.evaluations = { ...this.initialEvaluations };
            this.recommendation = this.initialRecommendation;
            this.isLocked = this.readonly;
        },
        saveToLocalStorage() {
            localStorage.setItem(this.storageKey, JSON.stringify({
                evaluations: this.evaluations,
                recommendation: this.recommendation,
                isLocked: this.isLocked
            }));
        },
        saveScrollPosition() {
            localStorage.setItem(this.storageKey + '_scroll', window.scrollY);
        },
        select(subId, status, score) {
            if (this.isLocked) return;
            if (status === 'not_applicable') {
                Vue.set(this.evaluations, subId, { status, score: null });
            } else if (status === 'not_available') {
                Vue.set(this.evaluations, subId, { status, score: 0 });
            } else if (score !== '') {
                Vue.set(this.evaluations, subId, { status, score: parseInt(score) });
            }
        },
        getStatus(id) {
            return this.evaluations[id]?.status || null;
        },
        getScore(id) {
            return this.evaluations[id]?.score || '';
        },
        clearAll() {
            if (this.isLocked) return;
            if (!confirm('Clear all evaluations?')) return;
            this.evaluations = {};
            this.recommendation = '';
            localStorage.removeItem(this.storageKey);
        },
        async submitEvaluation() {
            if (this.isLocked) return;
            if (!this.isComplete) {
                alert('Please evaluate all checklist items before submitting.');
                return;
            }

            this.saving = true;
            try {
                const response = await fetch(this.submitUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken,
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
                });

                const data = await response.json();
                if (!response.ok) {
                    throw new Error(data.message || 'Submission failed');
                }

                localStorage.removeItem(this.storageKey);
                localStorage.removeItem(this.storageKey + '_scroll');
                window.location.href = data.redirect;
            } catch (error) {
                alert(error.message);
            } finally {
                this.saving = false;
            }
        }
    },
    template: `
        <div>
           <div>
            {{-- LOCK / FINAL WARNING --}}
            <div v-if="isFinalized" class="alert alert-success d-flex justify-content-center text-center">
                <i class="bx bx-lock"></i>
                You already finalized your evaluation. Editing is locked.
            </div>
            <div v-else-if="isSubmitted && isLocked" class="alert alert-warning d-flex justify-content-between align-items-center">
                <div>
                    <i class="bx bx-lock"></i>
                    You already evaluated this area. Click ‘Edit Evaluation’ to make changes if needed.
                </div>
                <button class="btn btn-sm btn-warning" @click="unlockForm">
                    <i class="bx bx-edit"></i> Edit Evaluation
                </button>
            </div>

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
                    <template v-for="parameter in parameters">
                        <tr class="table-secondary fw-semibold">
                            <td colspan="6">@{{ parameter.name }}</td>
                        </tr>
                        <tr v-for="sub in parameter.sub_parameters" :key="sub.id">
                            <td style="padding-left: 30px">@{{ sub.name }}</td>
                            <td class="text-center">
                                <select class="form-select form-select-sm"
                                        :disabled="isLocked"
                                        :value="getStatus(sub.id) === 'available' ? getScore(sub.id) : ''"
                                        @change="select(sub.id, 'available', $event.target.value)">
                                    <option value="">—</option>
                                    <option value="5">5</option>
                                    <option value="4">4</option>
                                    <option value="3">3</option>
                                </select>
                            </td>
                            <td class="text-center">
                                <select class="form-select form-select-sm"
                                        :disabled="isLocked"
                                        :value="getStatus(sub.id) === 'inadequate' ? getScore(sub.id) : ''"
                                        @change="select(sub.id, 'inadequate', $event.target.value)">
                                    <option value="">—</option>
                                    <option value="2">2</option>
                                    <option value="1">1</option>
                                </select>
                            </td>
                            <td class="text-center">
                                <input type="radio"
                                       :disabled="isLocked"
                                       :name="'eval_' + sub.id"
                                       :checked="getStatus(sub.id) === 'not_available'"
                                       @change="select(sub.id, 'not_available', 0)">
                            </td>
                            <td class="text-center">
                                <input type="radio"
                                       :disabled="isLocked"
                                       :name="'eval_' + sub.id"
                                       :checked="getStatus(sub.id) === 'not_applicable'"
                                       @change="select(sub.id, 'not_applicable', null)">
                            </td>
                            <td class="text-center">
                                <a v-if="sub.uploads_count > 0" :href="sub.uploads_url" class="btn btn-sm btn-outline-primary">
                                    <i class="bx bxs-file-pdf"></i>
                                    <span>@{{ sub.uploads_count }}</span>
                                </a>
                                <span v-else class="text-muted small">No available document</span>
                            </td>
                        </tr>
                    </template>
                </tbody>
                <tfoot class="fw-semibold">
                    <tr>
                        <td>Total</td>
                        <td class="text-center">@{{ totals.available }}</td>
                        <td class="text-center">@{{ totals.inadequate }}</td>
                        <td class="text-center">@{{ totals.notAvailable }}</td>
                        <td class="text-center">@{{ totals.notApplicable }}</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Area Mean</td>
                        <td colspan="5" class="text-center fs-5 fw-bold">@{{ mean }}</td>
                    </tr>
                </tfoot>
            </table>

            {{-- RECOMMENDATION --}}
            <div class="mt-4">
                <label class="fw-bold">Recommendations</label>
                <textarea class="form-control"
                          rows="4"
                          :disabled="isLocked"
                          v-model="recommendation"></textarea>
            </div>

            {{-- ACTION BUTTONS --}}
            <div class="mt-4 d-flex justify-content-end gap-2">
                <button type="button"
                        class="btn btn-outline-danger"
                        :disabled="isLocked"
                        @click="clearAll()">
                    <i class="bx bx-trash"></i> Clear All
                </button>
                <button type="button"
                        class="btn btn-primary"
                        :disabled="isLocked || !isComplete || saving"
                        @click="submitEvaluation()">
                    <i class="bx bx-send"></i> Submit Evaluation
                </button>
            </div>

            {{-- REMINDER --}}
            <div class="mt-2 text-end"
                 v-if="!isComplete"
                 style="font-size: 13px;">
                <i class="bx bx-info-circle text-warning"></i>
                <span class="text-warning">
                    Please evaluate all checklist items before submitting.
                </span>
            </div>
        </div>
    `
});

new Vue({ el: '#app' });
</script>
@endsection