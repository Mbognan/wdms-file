@extends('admin.layouts.master')

@section('contents')

<div class="container-xxl container-p-y"
     x-data="areaEvaluation({{ $isEvaluated ? 'true' : 'false' }})"
     x-init="init()">

    {{-- HEADER --}}
    <h4 class="fw-bold mb-1">{{ $programArea->area->area_name }}</h4>
    <p class="text-muted mb-4">Program Area Evaluation</p>

    {{-- ALREADY EVALUATED WARNING --}}
    @if($isEvaluated)
        <div class="alert alert-warning">
            <i class="bx bx-lock"></i>
            This area has already been evaluated. Editing is disabled.
        </div>
    @endif

    {{-- AREA EVALUATION --}}
    <div class="card mb-4">
        <div class="card-body">
            <h6 class="fw-bold mb-3">Area Evaluation</h6>

            <table class="table table-bordered table-sm align-middle">
                <thead class="table-light">
                    <tr class="text-center">
                        <th style="width:35%">Checklist Item</th>
                        <th>Available<br><small>(5–4–3)</small></th>
                        <th>Available but Inadequate<br><small>(2–1)</small></th>
                        <th>Not Available<br><small>(0)</small></th>
                        <th>Not Applicable<br><small>(NA)</small></th>
                        <th style="width:10%">Documents</th>
                    </tr>
                </thead>

                <tbody>
                @foreach($parameters as $parameter)

                    {{-- PARAMETER HEADER --}}
                    <tr class="table-secondary fw-semibold">
                        <td colspan="6">{{ $parameter->parameter_name }}</td>
                    </tr>

                    @foreach($parameter->sub_parameters as $sub)
                    <tr>
                        <td>{{ $sub->sub_parameter_name }}</td>

                        {{-- AVAILABLE --}}
                        <td class="text-center">
                            <select class="form-select form-select-sm"
                                :disabled="locked"
                                :value="getStatus('{{ $sub->id }}') === 'available'
                                    ? getScore('{{ $sub->id }}') : ''"
                                @change="select('{{ $sub->id }}','available',$event.target.value)">
                                <option value="">—</option>
                                <option value="5">5</option>
                                <option value="4">4</option>
                                <option value="3">3</option>
                            </select>
                        </td>

                        {{-- INADEQUATE --}}
                        <td class="text-center">
                            <select class="form-select form-select-sm"
                                :disabled="locked"
                                :value="getStatus('{{ $sub->id }}') === 'inadequate'
                                    ? getScore('{{ $sub->id }}') : ''"
                                @change="select('{{ $sub->id }}','inadequate',$event.target.value)">
                                <option value="">—</option>
                                <option value="2">2</option>
                                <option value="1">1</option>
                            </select>
                        </td>

                        {{-- NOT AVAILABLE --}}
                        <td class="text-center">
                            <input type="radio"
                                   :disabled="locked"
                                   name="eval_{{ $sub->id }}"
                                   :checked="getStatus('{{ $sub->id }}') === 'not_available'"
                                   @change="select('{{ $sub->id }}','not_available',0)">
                        </td>

                        {{-- NOT APPLICABLE --}}
                        <td class="text-center">
                            <input type="radio"
                                   :disabled="locked"
                                   name="eval_{{ $sub->id }}"
                                   :checked="getStatus('{{ $sub->id }}') === 'not_applicable'"
                                   @change="select('{{ $sub->id }}','not_applicable','NA')">
                        </td>

                        {{-- DOCUMENTS --}}
                        <td class="text-center">
                            <a href="{{ route('subparam.uploads.index', [
                                'subParameter'   => $sub->id,
                                'infoId'         => $infoId,
                                'levelId'        => $levelId,
                                'programId'      => $programId,
                                'programAreaId'  => $programAreaId,
                            ]) }}"
                            class="btn btn-sm btn-outline-primary">
                               <i class="bx bxs-file-pdf"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                @endforeach
                </tbody>

                {{-- TOTALS --}}
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
                        <td colspan="5"
                            class="text-center fs-5 fw-bold"
                            x-text="mean"></td>
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
                        :disabled="locked"
                        @click="submitEvaluation()">
                    <i class="bx bx-send"></i> Submit Evaluation
                </button>
            </div>

        </div>
    </div>
</div>

{{-- ALPINE SCRIPT --}}
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('areaEvaluation', (locked = false) => ({

        locked,

        accredInfoId: {{ $infoId }},
        levelId: {{ $levelId }},
        programId: {{ $programId }},
        programAreaId: {{ $programAreaId }},

        storageKey: 'area-eval-{{ $programAreaId }}-{{ $levelId }}-{{ $programId }}',

        evaluations: {},
        totals: {
            available: 0,
            inadequate: 0,
            not_available: 0,
            not_applicable: 0
        },
        mean: '0.00',
        recommendation: '',

        init() {
            if (this.locked) return

            const saved = localStorage.getItem(this.storageKey)
            if (saved) {
                const data = JSON.parse(saved)
                this.evaluations = data.evaluations ?? {}
                this.recommendation = data.recommendation ?? ''
            }
            this.compute()
        },

        getStatus(id) {
            return this.evaluations[id]?.status ?? null
        },

        getScore(id) {
            return this.evaluations[id]?.score ?? ''
        },

        select(subId, status, score) {
            if (this.locked) return

            delete this.evaluations[subId]

            if (status === 'not_applicable') {
                this.evaluations[subId] = { status, score: null }
            } else if (status === 'not_available') {
                this.evaluations[subId] = { status, score: 0 }
            } else if (score !== '') {
                this.evaluations[subId] = {
                    status,
                    score: parseInt(score)
                }
            }

            this.compute()
            this.save()
        },

        compute() {
            this.totals = {
                available: 0,
                inadequate: 0,
                not_available: 0,
                not_applicable: 'N/A'
            }

            let totalScore = 0
            let applicableCount = 0

            Object.values(this.evaluations).forEach(item => {
                if (item.status === 'available' || item.status === 'inadequate') {
                    totalScore += item.score
                    applicableCount++
                    this.totals[item.status] += item.score
                } else if (item.status === 'not_available') {
                    applicableCount++
                }
            })

            this.mean = applicableCount
                ? (totalScore / applicableCount).toFixed(2)
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

            if (!Object.keys(this.evaluations).length) {
                alert('Please evaluate at least one item.')
                return
            }

            try {
                const response = await fetch(
                    '{{ route('accreditation-evaluations.store') }}',
                    {
                        method: 'POST',
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
                    }
                )

                const data = await response.json()

                showToast(data.message)
                localStorage.removeItem(this.storageKey)

                // Redijrect to evaluation summary
                window.location.href = data.redirect


            } catch (e) {
                console.error(e)
                showToast(e.message ?? 'Failed to save evaluation.')
            }
        }
    }))
})
</script>

@endsection
