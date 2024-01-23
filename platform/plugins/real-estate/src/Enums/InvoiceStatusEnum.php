<?php

namespace Botble\RealEstate\Enums;

use Botble\Base\Supports\Enum;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

/**
 * @method static InvoiceStatusEnum PENDING()
 * @method static InvoiceStatusEnum PROCESSING()
 * @method static InvoiceStatusEnum COMPLETED()
 * @method static InvoiceStatusEnum CANCELED()
 * @method static InvoiceStatusEnum RETURNED()
 */
class InvoiceStatusEnum extends Enum
{
    public const PENDING = 'pending';

    public const PROCESSING = 'processing';

    public const COMPLETED = 'completed';

    public const CANCELED = 'canceled';

    public static $langPath = 'plugins/real-estate::invoice.statuses';

    public function toHtml(): HtmlString|string
    {
        $color = match ($this->value) {
            self::PENDING => 'warning',
            self::PROCESSING => 'info',
            self::COMPLETED => 'success',
            self::CANCELED => 'danger',
            self::COMPLETED => 'success',
            default => null,
        };

        return Blade::render(sprintf('<x-core::badge color="%s" label="%s" />', $color, $this->label()));
    }
}
