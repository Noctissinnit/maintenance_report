{{-- Reusable Stat Card Component --}}
<div class="col-lg-3 col-md-6 mb-3">
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-label">{{ $label }}</span>
            @if($showTrend ?? false)
                <span class="stat-trend {{ $trendClass ?? 'neutral' }}">
                    <i class="bi {{ $trendIcon ?? 'bi-dash-circle' }}"></i>
                </span>
            @endif
        </div>
        <div class="stat-value">{{ $value }}</div>
        @if($unit ?? false)
            <div class="stat-unit">{{ $unit }}</div>
        @endif
        @if($subtitle ?? false)
            <div class="stat-subtitle">{{ $subtitle }}</div>
        @endif
    </div>
</div>
