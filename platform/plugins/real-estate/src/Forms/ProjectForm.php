<?php

namespace Botble\RealEstate\Forms;

use Botble\Base\Facades\Assets;
use Botble\Base\Forms\FieldOptions\ContentFieldOption;
use Botble\Base\Forms\FieldOptions\DescriptionFieldOption;
use Botble\Base\Forms\FieldOptions\NameFieldOption;
use Botble\Base\Forms\FieldOptions\OnOffFieldOption;
use Botble\Base\Forms\FieldOptions\StatusFieldOption;
use Botble\Base\Forms\Fields\EditorField;
use Botble\Base\Forms\Fields\OnOffField;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\Fields\TextareaField;
use Botble\Base\Forms\Fields\TextField;
use Botble\Base\Forms\FormAbstract;
use Botble\Location\Fields\Options\SelectLocationFieldOption;
use Botble\Location\Fields\SelectLocationField;
use Botble\RealEstate\Enums\CustomFieldEnum;
use Botble\RealEstate\Enums\ProjectStatusEnum;
use Botble\RealEstate\Facades\RealEstateHelper;
use Botble\RealEstate\Forms\Fields\CategoryMultiField;
use Botble\RealEstate\Http\Requests\ProjectRequest;
use Botble\RealEstate\Models\Currency;
use Botble\RealEstate\Models\CustomField;
use Botble\RealEstate\Models\Facility;
use Botble\RealEstate\Models\Feature;
use Botble\RealEstate\Models\Investor;
use Botble\RealEstate\Models\Project;

class ProjectForm extends FormAbstract
{
    public function setup(): void
    {
        Assets::addStyles(['datetimepicker'])
            ->addScriptsDirectly([
                'vendor/core/plugins/real-estate/js/real-estate.js',
                'vendor/core/plugins/real-estate/js/components.js',
                'vendor/core/plugins/real-estate/js/custom-fields.js',
            ])
            ->addStylesDirectly('vendor/core/plugins/real-estate/css/real-estate.css');

        Assets::usingVueJS();

        $investors = Investor::query()->pluck('name', 'id')->all();

        $currencies = Currency::query()->pluck('title', 'id')->all();

        $selectedFeatures = [];
        if ($this->getModel()) {
            $selectedFeatures = $this->getModel()->features()->pluck('id')->all();
        }

        $features = Feature::query()->select(['id', 'name'])->get();

        $facilities = Facility::query()->select(['id', 'name'])->get();
        $selectedFacilities = [];
        if ($this->getModel()) {
            $selectedFacilities = $this->getModel()->facilities()->select('re_facilities.id', 'distance')->get();
        }

        $selectedCategories = [];
        if ($this->getModel()) {
            $selectedCategories = $this->getModel()->categories()->pluck('category_id')->all();
        }

        if (! $this->formHelper->hasCustomField('categoryMulti')) {
            $this->formHelper->addCustomField('categoryMulti', CategoryMultiField::class);
        }

        $customFields = CustomField::query()->select(['name', 'id', 'type'])->get();

        $this
            ->setupModel(new Project())
            ->setValidatorClass(ProjectRequest::class)
            ->add('name', TextField::class, NameFieldOption::make()->required()->toArray())
            ->add('description', TextareaField::class, DescriptionFieldOption::make()->toArray())
            ->add(
                'is_featured',
                OnOffField::class,
                OnOffFieldOption::make()
                    ->label(trans('core/base::forms.is_featured'))
                    ->defaultValue(false)
                    ->toArray()
            )
            ->add('content', EditorField::class, ContentFieldOption::make()->allowedShortcodes()->toArray())
            ->add('images[]', 'mediaImages', [
                'label' => trans('plugins/real-estate::property.form.images'),
                'values' => $this->getModel()->id ? $this->getModel()->images : [],
            ])
            ->when(is_plugin_active('location'), function (FormAbstract $form) {
                $form->add(
                    'location_data',
                    SelectLocationField::class,
                    SelectLocationFieldOption::make()->toArray()
                );
            })
            ->add('location', 'text', [
                'label' => trans('plugins/real-estate::project.form.location'),
                'required' => true,
                'attr' => [
                    'placeholder' => trans('plugins/real-estate::project.form.location'),
                    'data-counter' => 300,
                ],
            ])
            ->add('rowOpen', 'html', [
                'html' => '<div class="row">',
            ])
            ->add('latitude', 'text', [
                'label' => trans('plugins/real-estate::property.form.latitude'),
                'wrapper' => [
                    'class' => 'form-group mb-3 col-md-6',
                ],
                'attr' => [
                    'placeholder' => 'Ex: 1.462260',
                    'data-counter' => 25,
                ],
                'help_block' => [
                    'text' => trans('plugins/real-estate::property.form.latitude_helper'),
                    'attr' => [
                        'href' => 'https://www.latlong.net/convert-address-to-lat-long.html',
                        'target' => '_blank',
                        'rel' => 'nofollow',
                    ],
                ],
            ])
            ->add('longitude', 'text', [
                'label' => trans('plugins/real-estate::property.form.longitude'),
                'wrapper' => [
                    'class' => 'form-group mb-3 col-md-6',
                ],
                'attr' => [
                    'placeholder' => 'Ex: 103.812530',
                    'data-counter' => 25,
                ],
                'help_block' => [
                    'text' => trans('plugins/real-estate::property.form.longitude_helper'),
                    'attr' => [
                        'href' => 'https://www.latlong.net/convert-address-to-lat-long.html',
                        'target' => '_blank',
                        'rel' => 'nofollow',
                    ],
                ],
            ])
            ->add('rowClose', 'html', [
                'html' => '</div>',
            ])
            ->add('rowOpen1', 'html', [
                'html' => '<div class="row">',
            ])
            ->add('number_block', 'number', [
                'label' => trans('plugins/real-estate::project.form.number_block'),
                'wrapper' => [
                    'class' => 'form-group mb-3 col-md-4',
                ],
                'attr' => [
                    'placeholder' => trans('plugins/real-estate::project.form.number_block'),
                ],
            ])
            ->add('number_floor', 'number', [
                'label' => trans('plugins/real-estate::project.form.number_floor'),
                'wrapper' => [
                    'class' => 'form-group mb-3 col-md-4',
                ],
                'attr' => [
                    'placeholder' => trans('plugins/real-estate::project.form.number_floor'),
                ],
            ])
            ->add('number_flat', 'number', [
                'label' => trans('plugins/real-estate::project.form.number_flat'),
                'wrapper' => [
                    'class' => 'form-group mb-3 col-md-4',
                ],
                'attr' => [
                    'placeholder' => trans('plugins/real-estate::project.form.number_flat'),
                ],
            ])
            ->add('rowClose1', 'html', [
                'html' => '</div>',
            ])
            ->add('rowOpen2', 'html', [
                'html' => '<div class="row">',
            ])
            ->add('price_from', 'text', [
                'label' => trans('plugins/real-estate::project.form.price_from'),
                'wrapper' => [
                    'class' => 'form-group mb-3 col-md-4',
                ],
                'attr' => [
                    'placeholder' => trans('plugins/real-estate::project.form.price_from'),
                    'class' => 'form-control input-mask-number',
                    'data-thousands-separator' => RealEstateHelper::getThousandSeparatorForInputMask(),
                    'data-decimal-separator' => RealEstateHelper::getDecimalSeparatorForInputMask(),
                ],
            ])
            ->add('price_to', 'text', [
                'label' => trans('plugins/real-estate::project.form.price_to'),
                'wrapper' => [
                    'class' => 'form-group mb-3 col-md-4',
                ],
                'attr' => [
                    'placeholder' => trans('plugins/real-estate::project.form.price_to'),
                    'class' => 'form-control input-mask-number',
                    'data-thousands-separator' => RealEstateHelper::getThousandSeparatorForInputMask(),
                    'data-decimal-separator' => RealEstateHelper::getDecimalSeparatorForInputMask(),
                ],
            ])
            ->add('currency_id', 'customSelect', [
                'label' => trans('plugins/real-estate::project.form.currency'),
                'wrapper' => [
                    'class' => 'form-group mb-3 col-md-4',
                ],
                'attr' => [
                    'class' => 'form-control select-full',
                ],
                'choices' => $currencies,
            ])
            ->add('rowClose2', 'html', [
                'html' => '</div>',
            ])
            ->addMetaBoxes([
                'features' => [
                    'title' => trans('plugins/real-estate::property.form.features'),
                    'content' => view(
                        'plugins/real-estate::partials.form-features',
                        compact('selectedFeatures', 'features')
                    )->render(),
                    'priority' => 1,
                ],
                'facilities' => [
                    'title' => trans('plugins/real-estate::project.distance_key'),
                    'content' => view(
                        'plugins/real-estate::partials.form-facilities',
                        compact('facilities', 'selectedFacilities')
                    ),
                    'priority' => 0,
                ],
            ])
            ->add('status', SelectField::class, StatusFieldOption::make()->choices(ProjectStatusEnum::labels())->toArray())
            ->add('categories[]', 'categoryMulti', [
                'label' => trans('plugins/real-estate::project.form.categories'),
                'required' => true,
                'choices' => get_property_categories_with_children(),
                'value' => old('categories', $selectedCategories),
            ])
            ->add('investor_id', 'customSelect', [
                'label' => trans('plugins/real-estate::project.form.investor'),
                'attr' => [
                    'class' => 'form-control select-search-full',
                ],
                'choices' => [0 => trans('plugins/real-estate::project.select_investor')] + $investors,
            ])
            ->add('unique_id', 'text', [
                'label' => trans('plugins/real-estate::project.unique_id'),
                'attr' => [
                    'placeholder' => trans('plugins/real-estate::project.unique_id'),
                    'class' => 'form-control',
                ],
            ])
            ->add('date_finish', 'datePicker', [
                'label' => trans('plugins/real-estate::project.form.date_finish'),
            ])
            ->add('date_sell', 'datePicker', [
                'label' => trans('plugins/real-estate::project.form.date_sell'),
            ])
            ->setBreakFieldPoint('status');

        if (RealEstateHelper::isEnabledCustomFields()) {
            $this->addMetaBoxes([
                'custom_fields_box' => [
                    'title' => trans('plugins/real-estate::custom-fields.name'),
                    'content' => view('plugins/real-estate::custom-fields.custom-fields', [
                        'options' => CustomFieldEnum::labels(),
                        'customFields' => $customFields,
                        'model' => $this->model,
                        'ajax' => is_in_admin(true) ? route('real-estate.custom-fields.get-info') : route('public.account.custom-fields.get-info'),
                    ]),
                    'priority' => 0,
                ],
            ]);
        }
    }
}
