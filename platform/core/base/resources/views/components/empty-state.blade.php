@props([
    'title' => null,
    'subtitle' => null,
    'icon' => 'ti ti-ghost',
])

<div class="empty">
    @if ($icon)
        <div class="empty-icon">
            <x-core-base::icon :name="$icon" />
        </div>
    @endif
    @if ($title)
        <p class="empty-title">
            {!! $title !!}
        </p>
    @endif
    @if ($subtitle)
        <p class="empty-subtitle text-muted">
            {!! $subtitle !!}
        </p>
    @endif
    @if (isset($action))
        <div class="empty-action">
            {!! $action !!}
        </div>
    @endif
</div>
