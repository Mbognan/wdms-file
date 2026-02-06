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
        <tr class="table-secondary fw-semibold">
            <td colspan="6">{{ $parameter->parameter_name }}</td>
        </tr>

        @foreach($parameter->sub_parameters as $sub)
        <tr>
            <td>{{ $sub->sub_parameter_name }}</td>

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