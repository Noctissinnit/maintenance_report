{{-- Section Title Component --}}
<div class="section-title">
    @if($icon ?? false)
        <i class="bi {{ $icon }}"></i>
    @endif
    {{ $title }}
</div>
{{ $slot }}
