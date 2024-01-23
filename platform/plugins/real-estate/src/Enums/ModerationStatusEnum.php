<?php

namespace Botble\RealEstate\Enums;

use Botble\Base\Supports\Enum;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

/**
 * @method static ModerationStatusEnum PENDING()
 * @method static ModerationStatusEnum APPROVED()
 * @method static ModerationStatusEnum REJECTED()
 */
class ModerationStatusEnum extends Enum
{
    public const PENDING = 'pending';

    public const APPROVED = 'approved';

    public const REJECTED = 'rejected';

    public static $langPath = 'plugins/real-estate::property.moderation-statuses';

    public function toHtml(): HtmlString|string|null
    {
        $color = match ($this->value) {
            self::PENDING => 'warning',
            self::APPROVED => 'success',
            self::REJECTED => 'danger',
            default => null,
        };

        return Blade::render(sprintf('<x-core::badge color="%s" label="%s" />', $color, $this->label()));
    }
}
