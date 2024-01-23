<?php

namespace Botble\RealEstate\Supports;

use Botble\Page\Models\Page;
use Botble\RealEstate\Enums\ModerationStatusEnum;
use Botble\RealEstate\Enums\ProjectStatusEnum;
use Botble\RealEstate\Enums\PropertyStatusEnum;
use Botble\RealEstate\Enums\ReviewStatusEnum;
use Botble\RealEstate\Repositories\Interfaces\ProjectInterface;
use Botble\RealEstate\Repositories\Interfaces\PropertyInterface;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class RealEstateHelper
{
    protected string|null $projectsListingPageUrl = null;

    protected string|null $propertiesListingPageUrl = null;

    public function isRegisterEnabled(): bool
    {
        return setting('real_estate_enabled_register', true) && $this->isLoginEnabled();
    }

    public function isLoginEnabled(): bool
    {
        return setting('real_estate_enabled_login', true);
    }

    public function isDisabledPublicProfile(): bool
    {
        return setting('real_estate_disabled_public_profile', false);
    }

    public function propertyExpiredDays(): int
    {
        $days = (int)setting('property_expired_after_days');

        if ($days > 0) {
            return $days;
        }

        return 45;
    }

    public function getPropertyRelationsQuery(): array
    {
        return [
            'slugable:id,key,prefix,reference_id',
            'state:id,name',
            'city:id,name',
            'currency:id,is_default,exchange_rate,symbol,title,is_prefix_symbol',
            'categories' => function (Builder $query) {
                return $query
                    ->wherePublished()
                    ->orderBy('created_at', 'DESC')
                    ->orderBy('is_default', 'DESC')
                    ->orderBy('order', 'ASC')
                    ->select('re_categories.id', 're_categories.name');
            },
        ];
    }

    public function getProjectRelationsQuery(): array
    {
        return [
            'slugable:id,key,prefix,reference_id',
            'state:id,name',
            'city:id,name',
            'categories' => function (Builder $query) {
                return $query
                    ->wherePublished()
                    ->orderBy('created_at', 'DESC')
                    ->orderBy('is_default', 'DESC')
                    ->orderBy('order', 'ASC')
                    ->select('re_categories.id', 're_categories.name');
            },
        ];
    }

    public function isEnabledCreditsSystem(): bool
    {
        return setting('real_estate_enable_credits_system', 1) == 1;
    }

    public function getThousandSeparatorForInputMask(): string
    {
        return ',';
    }

    public function getDecimalSeparatorForInputMask(): string
    {
        return '.';
    }

    public function getPropertyDisplayQueryConditions(): array
    {
        $conditions = [
            're_properties.moderation_status' => ModerationStatusEnum::APPROVED,
        ];

        foreach ($this->exceptedPropertyStatuses() as $status) {
            $conditions[] = ['re_properties.status', '!=', $status];
        }

        return $conditions;
    }

    public function getProjectDisplayQueryConditions(): array
    {
        $conditions = [];

        foreach ($this->exceptedProjectsStatuses() as $status) {
            $conditions[] = ['re_projects.status', '!=', $status];
        }

        return $conditions;
    }

    public function exceptedPropertyStatuses(): array
    {
        $statuses = setting('real_estate_hide_properties_in_statuses');

        if ($statuses) {
            return json_decode($statuses, true);
        }

        return [PropertyStatusEnum::NOT_AVAILABLE];
    }

    public function exceptedProjectsStatuses(): array
    {
        $statuses = setting('real_estate_hide_projects_in_statuses');

        if ($statuses) {
            return json_decode($statuses, true);
        }

        return [ProjectStatusEnum::NOT_AVAILABLE];
    }

    public function isEnabledWishlist(): bool
    {
        return (int)setting('real_estate_enable_wishlist', 1) == 1;
    }

    protected function getPage(int|string|null $pageId): Page|Model|null
    {
        if (! $pageId) {
            return null;
        }

        return Page::query()
            ->wherePublished()
            ->where('id', $pageId)
            ->select(['id', 'name'])
            ->with(['slugable'])
            ->first();
    }

    public function getPropertiesListPageUrl(): ?string
    {
        if ($this->propertiesListingPageUrl) {
            return $this->propertiesListingPageUrl;
        }

        $pageId = theme_option('properties_list_page_id');

        if (! $pageId) {
            return route('public.properties');
        }

        $page = $this->getPage($pageId);

        $this->propertiesListingPageUrl = $page ? $page->url : route('public.properties');

        return $this->propertiesListingPageUrl;
    }

    public function getProjectsListPageUrl(): ?string
    {
        if ($this->projectsListingPageUrl) {
            return $this->projectsListingPageUrl;
        }

        $pageId = theme_option('projects_list_page_id');

        if (! $pageId) {
            return route('public.projects');
        }

        $page = $this->getPage($pageId);

        $this->projectsListingPageUrl = $page ? $page->url : route('public.projects');

        return $this->projectsListingPageUrl;
    }

    public function getPropertiesFilter(int|null $perPage = 12, array $extra = []): LengthAwarePaginator|Collection
    {
        $request = request();

        $perPage = $request->integer('per_page') ?: ($perPage ?? 12);

        $filters = $request->validate(apply_filters('properties_filter_validation_rules', [
            'keyword' => 'nullable|string|max:255',
            'location' => 'nullable|string',
            'city_id' => 'nullable|numeric',
            'city' => 'nullable|string',
            'state' => 'nullable|string',
            'state_id' => 'nullable|numeric',
            'type' => 'nullable|string',
            'bedroom' => 'nullable|numeric',
            'bathroom' => 'nullable|numeric',
            'floor' => 'nullable|numeric',
            'min_price' => 'nullable|numeric',
            'max_price' => 'nullable|numeric',
            'min_square' => 'nullable|numeric',
            'max_square' => 'nullable|numeric',
            'project' => 'nullable|string',
            'project_id' => 'nullable|string',
            'category_id' => 'nullable|numeric',
            'sort_by' => 'nullable|string',
            'locations' => 'nullable|array',
            'category_ids' => 'nullable|array',
        ]));

        $filters['keyword'] = $request->input('k');

        $params = array_merge([
            'paginate' => [
                'per_page' => $perPage,
                'current_paged' => $request->integer('page', 1),
            ],
            'order_by' => ['re_properties.created_at' => 'DESC'],
            'with' => RealEstateHelper::getPropertyRelationsQuery(),
        ], $extra);

        return app(PropertyInterface::class)->getProperties($filters, $params);
    }

    public function getProjectsFilter(int|null $perPage = 12, array $extra = []): LengthAwarePaginator|Collection
    {
        $request = request();

        $perPage = $request->integer('per_page') ?: ($perPage ?: 12);

        $filters = $request->validate(apply_filters('projects_filter_validation_rules', [
            'keyword' => 'nullable|string|max:255',
            'location' => 'nullable|string',
            'city_id' => 'nullable|numeric',
            'city' => 'nullable|string',
            'state' => 'nullable|string',
            'state_id' => 'nullable|numeric',
            'category_id' => 'nullable|numeric',
            'sort_by' => 'nullable|string',
            'blocks' => 'nullable|numeric',
            'min_price' => 'nullable|numeric',
            'max_price' => 'nullable|numeric',
            'min_floor' => 'nullable|numeric',
            'max_floor' => 'nullable|numeric',
            'min_flat' => 'nullable|numeric',
            'max_flat' => 'nullable|numeric',
            'locations' => 'nullable|array',
            'category_ids' => 'nullable|array',
        ]));

        $filters['keyword'] = $request->input('k');

        $params = array_merge([
            'paginate' => [
                'per_page' => $perPage,
                'current_paged' => $request->integer('page', 1),
            ],
            'order_by' => ['re_projects.created_at' => 'DESC'],
            'with' => self::getProjectRelationsQuery(),
        ], $extra);

        return app(ProjectInterface::class)->getProjects($filters, $params);
    }

    public function getPropertiesPerPageList(): array
    {
        return apply_filters(PROPERTIES_PER_PAGE_LIST, [
            9 => 9,
            12 => 12,
            15 => 15,
            30 => 30,
            45 => 45,
            60 => 60,
            120 => 120,
        ]);
    }

    public function getProjectsPerPageList(): array
    {
        return apply_filters(PROJECTS_PER_PAGE_LIST, [
            9 => 9,
            12 => 12,
            15 => 15,
            30 => 30,
            45 => 45,
            60 => 60,
            120 => 120,
        ]);
    }

    public function getSortByList(): array
    {
        return [
            'date_asc' => __('Oldest'),
            'date_desc' => __('Newest'),
            'price_asc' => __('Price (low to high)'),
            'price_desc' => __('Price (high to low)'),
            'name_asc' => __('Name (A-Z)'),
            'name_desc' => __('Name (Z-A)'),
        ];
    }

    public function getReviewExtraData(): array
    {
        if (! $this->isEnabledReview()) {
            return [];
        }

        return [
            'withCount' => [
                'reviews' => function ($query) {
                    $query->where('status', ReviewStatusEnum::APPROVED);
                },
            ],
            'withAvg' => ['reviews', 'star'],
        ];
    }

    public function isEnabledReview(): bool
    {
        return (bool)setting('real_estate_enable_review_feature', true);
    }

    public function getMapCenterLatLng(): array
    {
        $center = theme_option('latitude_longitude_center_on_properties_page', '');
        $latLng = [];
        if ($center) {
            $center = explode(',', $center);
            if (count($center) == 2) {
                $latLng = [trim($center[0]), trim($center[1])];
            }
        }

        if (! $latLng) {
            $latLng = [43.615134, -76.393186];
        }

        return $latLng;
    }

    public function isEnabledCustomFields(): bool
    {
        return (bool)setting('real_estate_enabled_custom_fields_feature', true);
    }

    public function getSquareUnits(): array
    {
        return [
            'm²' => __('m²'),
            'ft2' => __('ft2'),
            'yd2' => __('yd2'),
        ];
    }

    public function maxFilesizeUploadByAgent(): int
    {
        $size = setting('real_estate_max_filesize_upload_by_agent');

        if (! $size) {
            $size = setting('max_upload_filesize') ?: 10;
        }

        return (int)$size;
    }

    public function maxPropertyImagesUploadByAgent(): int
    {
        return (int)setting('real_estate_max_property_images_upload_by_agent', 20);
    }

    public function hideAgentInfoInPropertyDetailPage(): bool
    {
        return (bool)setting('real_estate_hide_agent_info_in_property_detail_page', false);
    }

    public function getMapTileLayer(): string
    {
        return 'https://mt0.google.com/vt/lyrs=m&x={x}&y={y}&z={z}&hl=' . app()->getLocale();
    }

    public function getMandatoryFieldsAtConsultForm(): array
    {
        return [
            'email' => trans('plugins/real-estate::consult.form_email'),
            'phone' => trans('plugins/real-estate::consult.form_phone'),
        ];
    }

    public function enabledMandatoryFieldsAtConsultForm(): array
    {
        $fields = setting('real_estate_mandatory_fields_at_consult_form');

        if (! $fields) {
            return array_keys($this->getMandatoryFieldsAtConsultForm());
        }

        return json_decode((string)$fields, true);
    }

    public function getHiddenFieldsAtConsultForm(): array
    {
        $fields = setting('real_estate_hide_fields_at_consult_form');

        if (! $fields) {
            return [];
        }

        return json_decode((string)$fields, true);
    }

    public function hasEnabledFieldAtConsultForm(string $field): bool
    {
        return in_array($field, $this->enabledMandatoryFieldsAtConsultForm());
    }

    public function isHiddenFieldAtConsultForm(string $field): bool
    {
        return in_array($field, $this->getHiddenFieldsAtConsultForm());
    }
}
