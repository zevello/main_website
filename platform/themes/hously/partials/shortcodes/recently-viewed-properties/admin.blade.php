<div class="mb-3">
    <label class="form-label">{{ __('Title') }}</label>
    <input name="title" value="{{ Arr::get($attributes, 'title') }}" class="form-control">
</div>

<div class="mb-3">
    <label class="form-label">{{ __('Subtitle') }}</label>
    <textarea name="subtitle" class="form-control">{{ Arr::get($attributes, 'subtitle') }}</textarea>
</div>

<div class="mb-3">
    <label class="form-label">{{ __('Limit') }}</label>
    <input type="number" name="limit" value="{{ Arr::get($attributes, 'limit', 6) }}" class="form-control">
</div>

<div class="mb-3">
    <label class="form-label">{{ __('layout') }}</label>
    {!! Form::customSelect('layout', ['grid' => __('Grid'), 'list' => __('List')], Arr::get($attributes, 'layout')) !!}
</div>

