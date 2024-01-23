<?php

namespace Botble\Base\Forms\FieldOptions;

use Botble\Base\Facades\BaseHelper;
use Carbon\Carbon;

class DatePickerFieldOption extends TextareaFieldOption
{
    public static function make(): static
    {
        return parent::make()
            ->defaultValue(BaseHelper::formatDate(Carbon::now()));
    }
}
