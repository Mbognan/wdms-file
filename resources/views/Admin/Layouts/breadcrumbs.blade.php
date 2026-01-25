{{-- Breadcrumb Partial --}}
@php
    // Default to empty array if not passed
    $breadcrumbs = $breadcrumbs ?? [];
@endphp

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        @foreach ($breadcrumbs as $index => $item)
            @if ($index === count($breadcrumbs) - 1)
                <li class="breadcrumb-item active" aria-current="page">{{ $item['label'] }}</li>
            @else
                <li class="breadcrumb-item">
                    <a href="{{ $item['url'] ?? 'javascript:void(0);' }}">{{ $item['label'] }}</a>
                </li>
            @endif
        @endforeach
    </ol>
</nav>
