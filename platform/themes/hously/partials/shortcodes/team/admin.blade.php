@php
    $accountIds = explode(',', Arr::get($attributes, 'account_ids'));
@endphp

<div class="mb-3">
    <label class="form-label">{{ __('Title') }}</label>
    <input type="text" name="title" value="{{ Arr::get($attributes, 'title') }}" class="form-control" placeholder="{{ __('Title') }}">
</div>

<div class="mb-3">
    <label class="form-label">{{ __('Subtitle') }}</label>
    <input type="text" name="subtitle" value="{{ Arr::get($attributes, 'subtitle') }}" class="form-control" placeholder="{{ __('Subtitle') }}">
</div>

<div class="mb-3">
    <label class="form-label">{{ __('Choose account') }}</label>
    <select class="select-full" name="account_ids" multiple>
        @foreach($accounts as $account)
            <option @selected(in_array($account->id, $accountIds)) value="{{ $account->id }}">{{ $account->name }} - {{  $account->email}}</option>
        @endforeach
    </select>
</div>
