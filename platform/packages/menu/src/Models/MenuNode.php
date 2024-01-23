<?php

namespace Botble\Menu\Models;

use Botble\Base\Casts\SafeContent;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Request;

class MenuNode extends BaseModel
{
    protected $table = 'menu_nodes';

    protected $fillable = [
        'menu_id',
        'parent_id',
        'reference_id',
        'reference_type',
        'url',
        'icon_font',
        'title',
        'css_class',
        'target',
        'has_child',
        'position',
    ];

    protected $casts = [
        'title' => SafeContent::class,
        'url' => SafeContent::class,
        'css_class' => SafeContent::class,
        'icon_font' => SafeContent::class,
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(MenuNode::class, 'parent_id');
    }

    public function child(): HasMany
    {
        return $this->hasMany(MenuNode::class, 'parent_id')->orderBy('position');
    }

    public function reference(): MorphTo
    {
        return $this->morphTo()->with(['slugable']);
    }

    protected function url(): Attribute
    {
        return Attribute::get(function ($value) {
            $value = html_entity_decode(BaseHelper::clean($value));

            if ($value) {
                return apply_filters(MENU_FILTER_NODE_URL, $value);
            }

            if (! $this->reference_type) {
                return '/';
            }

            if (! $this->reference) {
                return '/';
            }

            return (string)$this->reference->url;
        });
    }

    protected function title(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => str_replace('&amp;', '&', $value),
            get: function ($value) {
                if ($value) {
                    return $value;
                }

                if (! $this->reference_type || ! $this->reference) {
                    return $value;
                }

                return $this->reference->name;
            },
        );
    }

    protected function active(): Attribute
    {
        return Attribute::get(fn () => rtrim(url($this->url), '/') == rtrim(Request::url(), '/'));
    }
}
