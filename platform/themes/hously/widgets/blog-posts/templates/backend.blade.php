@php
    $type = [
        'popular' => __('Popular Posts'),
        'recent' => __('Recent Posts'),
    ];
@endphp

<div class="mb-3">
    <label class="form-label" for="widget-name">{{ __('Name') }}</label>
    <input type="text" class="form-control" name="name" value="{{ Arr::get($config, 'name') }}">
</div>
<div class="mb-3">
    <label class="form-label" for="type">{{ __('Post type') }}</label>
    {!! Form::customSelect('type', $type, Arr::get($config, 'type')) !!}
</div>
<div class="mb-3">
    <label class="form-label" for="limit">{{ __('Limit') }}</label>
    <input type="number" class="form-control" name="limit" value="{{ Arr::get($config, 'limit', 5) }}" placeholder="{{ __('Number posts to display') }}">
</div>
