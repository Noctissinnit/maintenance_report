{{-- Page Header Component --}}
<div class="page-header">
    <div>
        <h2 class="page-title">{{ $title }}</h2>
        <div class="page-subtitle">{{ $subtitle ?? '' }}</div>
    </div>
    @if($actions ?? false)
        <div class="header-actions">
            {{ $actions }}
        </div>
    @endif
</div>
