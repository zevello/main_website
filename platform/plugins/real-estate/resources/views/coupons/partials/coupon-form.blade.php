<div class="row">
    <div class="col-md-8">
        <x-core::card>
            <x-core::card.body>
                <x-core::form.text-input
                    :label="trans('plugins/real-estate::coupon.coupon_code')"
                    name="code"
                    :placeholder="trans('plugins/real-estate::coupon.coupon_code_placeholder')"
                    :value="old('code', $coupon)"
                >
                    <x-slot:append>
                        <x-core::button
                            data-url="{{ route('coupons.generate-coupon') }}"
                            data-bb-toggle="coupon-generate-code"
                        >
                            {{ trans('plugins/real-estate::coupon.generate_code_button') }}
                        </x-core::button>
                    </x-slot:append>
                </x-core::form.text-input>

                <div class="row">
                    <div class="col-md-6">
                        <x-core::form.select
                            :label="trans('plugins/real-estate::coupon.type')"
                            name="type"
                            :options="[
                                'percentage' => __('plugins/real-estate::coupon.types.percentage'),
                                'fixed' => __('plugins/real-estate::coupon.types.fixed'),
                            ]"
                            :value="old('type', $coupon)"
                        />
                    </div>

                    <div class="col-md-6">
                        <x-core::form.text-input
                            :label="trans('plugins/real-estate::coupon.value')"
                            type="number"
                            name="value"
                            :placeholder="trans('plugins/real-estate::coupon.value_placeholder')"
                            :value="old('value', $coupon)"
                            :group-flat="true"
                        >
                            <x-slot:append>
                                <span class="input-group-text icon-type">%</span>
                            </x-slot:append>
                        </x-core::form.text-input>
                    </div>
                </div>

                <div class="mb-3 position-relative">
                    <x-core::form.checkbox
                        :label="trans('plugins/real-estate::coupon.unlimited')"
                        name="is_unlimited"
                        :checked="old('quantity', $coupon) === null"
                    />
                </div>

                <x-core::form.text-input
                    type="number"
                    name="quantity"
                    :placeholder="trans('plugins/real-estate::coupon.quantity_placeholder')"
                    :value="old('quantity', $coupon)"
                    :wrapper-class="old('quantity', $coupon) === null ? 'd-none' : ''"
                />
            </x-core::card.body>
        </x-core::card>
    </div>

    <div class="col-md-4">
        <x-core::card class="mb-3">
            <x-core::card.body>
                <div class="gap-1 d-flex">
                    <x-core::form-group class="date-time-group">
                        <x-core::form.label>
                            {{ trans('plugins/real-estate::coupon.expires_date') }}
                        </x-core::form.label>
                        <div class="input-icon datepicker">
                            <input
                                type="text"
                                placeholder="{{ BaseHelper::getDateFormat() }}"
                                data-date-format="{{ BaseHelper::getDateFormat() }}"
                                name="expires_date"
                                class="form-control"
                                data-input
                                value="{{ old('expires_date', $coupon->expires_date ?? \Carbon\Carbon::now()->format(BaseHelper::getDateFormat())) }}"
                            />
                            <span class="input-icon-addon" type="button" title="toggle" data-toggle>
                                <x-core::icon name="ti ti-calendar" />
                            </span>
                        </div>
                    </x-core::form-group>
                    <x-core::form-group class="date-time-group">
                        <x-core::form.label>
                            {{ trans('plugins/real-estate::coupon.expires_time') }}
                        </x-core::form.label>
                        <div class="input-icon">
                            <input
                                type="text"
                                placeholder="hh:mm"
                                name="expires_time"
                                class="form-control time-picker timepicker timepicker-24"
                                value="{{ old('expires_time', $coupon->expires_date?->format('G:i') ?? \Carbon\Carbon::now()->format('G:i')) }}"
                            />
                            <span class="input-icon-addon" type="button" title="toggle" data-toggle>
                                <x-core::icon name="ti ti-clock" />
                            </span>
                        </div>
                    </x-core::form-group>
                </div>
                <x-core::form.checkbox
                    :label="trans('plugins/real-estate::coupon.never_expired')"
                    name="never_expired"
                    value="1"
                    :checked="old('never_expired', $coupon->expires_date === null)"
                />
            </x-core::card.body>
        </x-core::card>

        <x-core::card>
            <x-core::card.body>
                <x-core::button type="submit" color="primary">
                    {{ trans('plugins/real-estate::coupon.save_button') }}
                </x-core::button>
            </x-core::card.body>
        </x-core::card>
    </div>
</div>

@include('plugins/real-estate::coupons.partials.scripts', compact('jsValidator'))
