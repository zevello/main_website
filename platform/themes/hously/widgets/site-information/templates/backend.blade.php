<div class="mb-3">
    <label class="form-label" for="logo">{{ __('Logo') }}</label>
    {!! Form::mediaImage('logo', Arr::get($config, 'logo')) !!}
</div>

<div class="mb-3">
    <label class="form-label" for="widget-name">{{ __('URL') }}</label>
    <input type="text" id="url" name="url" class="form-control" value="{{ Arr::get($config, 'url') }}">
</div>

<div class="mb-3">
    <label class="form-label" for="description">{{ __('Description') }}</label>
    <textarea id="description" class="form-control" name="description">{{ Arr::get($config, 'description') }}</textarea>
</div>
