@extends('admin.layouts.master')

@section('contents')

<div class="container-xxl container-p-y"
     x-data="areaEvaluation()"
     x-init="init()">

    {{-- HEADER --}}
    <h4 class="fw-bold mb-1">{{ $programArea->area->area_name }}</h4>
    <p class="text-muted mb-4">Program Area Evaluation</p>

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

                        {{-- AVAILABLE (5–4–3) --}}
                        <td class="text-center">
                            <select class="form-select form-select-sm"
                                :value="getStatus('{{ $sub->id }}') === 'available'
                                    ? getScore('{{ $sub->id }}') : ''"
                                @change="select('{{ $sub->id }}','available',$event.target.value)">
                                <option value="">—</option>
                                <option value="5">5</option>
                                <option value="4">4</option>
                                <option value="3">3</option>
                            </select>
                        </td>

                        {{-- INADEQUATE (2–1) --}}
                        <td class="text-center">
                            <select class="form-select form-select-sm"
                                :value="getStatus('{{ $sub->id }}') === 'inadequate'
                                    ? getScore('{{ $sub->id }}') : ''"
                                @change="select('{{ $sub->id }}','inadequate',$event.target.value)">
                                <option value="">—</option>
                                <option value="2">2</option>
                                <option value="1">1</option>
                            </select>
                        </td>

                        {{-- NOT AVAILABLE (0) --}}
                        <td class="text-center">
                            <input type="radio"
                                   name="eval_{{ $sub->id }}"
                                   :checked="getStatus('{{ $sub->id }}') === 'not_available'"
                                   @change="select('{{ $sub->id }}','not_available',0)">
                        </td>

                        {{-- NOT APPLICABLE (NA) --}}
                        <td class="text-center">
                            <input type="radio"
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
                                <i class="bx bx-folder-open"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                @endforeach
                </tbody>

                {{-- TOTALS & MEAN --}}
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

            {{-- RECOMMENDATIONS --}}
            <div class="mt-4">
                <label class="fw-bold">Recommendations</label>
                <textarea class="form-control"
                          rows="4"
                          x-model="recommendation"></textarea>
            </div>

        </div>
    </div>
</div>

{{-- ALPINE SCRIPT --}}
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('areaEvaluation', () => ({

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

        /* ---------------------------
           INIT
        ---------------------------- */
        init() {
            const saved = localStorage.getItem(this.storageKey)
            if (saved) {
                const data = JSON.parse(saved)
                this.evaluations = data.evaluations ?? {}
                this.recommendation = data.recommendation ?? ''
            }
            this.compute()
        },

        /* ---------------------------
           HELPERS (UI REHYDRATION)
        ---------------------------- */
        getStatus(subId) {
            return this.evaluations[subId]?.status ?? null
        },

        getScore(subId) {
            return this.evaluations[subId]?.score ?? ''
        },

        /* ---------------------------
           SELECT
        ---------------------------- */
        select(subId, status, score) {

            if (score === '' || score === null) return

            this.evaluations[subId] = {
                status,
                score: score === 'NA' ? null : parseInt(score)
            }

            this.compute()
            this.save()
        },

        /* ---------------------------
           COMPUTE
        ---------------------------- */
        compute() {

            let totals = {
                available: 0,
                inadequate: 0,
                not_available: 0,
                not_applicable: 0
            }

            let totalScore = 0
            let applicableCount = 0

            Object.values(this.evaluations).forEach(item => {

                totals[item.status]++

                if (item.status !== 'not_applicable') {
                    totalScore += item.score
                    applicableCount++
                }
            })

            this.totals = totals

            this.mean = applicableCount
                ? (totalScore / applicableCount).toFixed(2)
                : '0.00'

            if (this.mean < 2.5) {
                this.recommendation =
                    'The area needs significant improvement to meet accreditation requirements.'
            } else if (this.mean < 3.5) {
                this.recommendation =
                    'The area generally complies but improvements are recommended.'
            } else {
                this.recommendation =
                    'The area meets accreditation standards and is ready for survey.'
            }
        },

        /* ---------------------------
           SAVE
        ---------------------------- */
        save() {
            localStorage.setItem(this.storageKey, JSON.stringify({
                evaluations: this.evaluations,
                recommendation: this.recommendation
            }))
        },

        /* ---------------------------
           CLEAR (OPTIONAL)
        ---------------------------- */
        clearAll() {
            localStorage.removeItem(this.storageKey)
            this.evaluations = {}
            this.recommendation = ''
            this.compute()
        }

    }))
})
</script>

@endsection
