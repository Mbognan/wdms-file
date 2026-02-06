<table class="table table-bordered table-sm align-middle">
    <thead class="table-light">
    <tr class="text-center">
        <th style="width:35%">Checklist Item</th>
        <th>Available</th>
        <th>Inadequate</th>
        <th>Not Available</th>
        <th>Not Applicable</th>
    </tr>
    </thead>

    <tbody>
    @foreach($parameters as $parameter)

        {{-- PARAMETER HEADER --}}
        <tr class="table-secondary fw-semibold">
            <td colspan="5">
                {{ $parameter->parameter_name }}
            </td>
        </tr>

        @foreach($parameter->sub_parameters as $sub)
            @php
                $rating = $ratings[$sub->id] ?? null;
                $label  = $rating?->ratingOption?->label;
            @endphp

            <tr>
                <td>{{ $sub->sub_parameter_name }}</td>

                <td class="text-center">
                    {{ $label === 'Available' ? $rating->score : ' ' }}
                </td>

                <td class="text-center">
                    {{ $label === 'Available but Inadequate' ? $rating->score : ' ' }}
                </td>

                <td class="text-center">
                    {{ $label === 'Not Available' ? '0' : ' ' }}
                </td>

                <td class="text-center">
                    {{ $label === 'Not Applicable' ? 'NA' : ' ' }}
                </td>
            </tr>
        @endforeach

    @endforeach
    </tbody>

    {{-- TOTALS --}}
    <tfoot class="fw-semibold">
    <tr>
        <td>Total</td>
        <td class="text-center">{{ $totals['available'] }}</td>
        <td class="text-center">{{ $totals['inadequate'] }}</td>
        <td class="text-center">{{ $totals['not_available'] }}</td>
        <td class="text-center">{{ $totals['not_applicable'] }}</td>
    </tr>
    <tr>
        <td>Area Mean</td>
        <td colspan="4" class="text-center fs-5 fw-bold">
            {{ $mean }}
        </td>
    </tr>
    </tfoot>
</table>

{{-- RECOMMENDATION --}}
<div class="mt-4">
    <label class="fw-bold">Recommendations</label>
    <textarea class="form-control" rows="4" disabled>
        {{ $evaluation->areaRecommendations->first()?->recommendation }}
    </textarea>
</div>

{{-- ACTIONS --}}
<div class="mt-4 text-end">
    <a href="{{ url()->previous() }}" class="btn btn-secondary">
        Back
    </a>
    <button onclick="window.print()" class="btn btn-outline-primary">
        Print
    </button>
</div>