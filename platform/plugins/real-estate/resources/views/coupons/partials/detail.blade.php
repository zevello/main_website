{!! Html::link(route('coupons.edit', $coupon), $coupon->code) !!}
<p class="text-muted mt-1 mb-0">{!! BaseHelper::clean(trans('plugins/real-estate::coupon.value_off', ['value' => "<strong>$value</strong>"])) !!}</p>
