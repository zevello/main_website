<?php

namespace Botble\RealEstate\Forms;

use Botble\Base\Facades\Assets;
use Botble\Base\Forms\FieldOptions\NameFieldOption;
use Botble\Base\Forms\Fields\TextField;
use Botble\Base\Forms\FormAbstract;
use Botble\RealEstate\Enums\CustomFieldEnum;
use Botble\RealEstate\Http\Requests\CustomFieldRequest;
use Botble\RealEstate\Models\CustomField;
use Illuminate\Support\Facades\Blade;

class CustomFieldForm extends FormAbstract
{
    public function setup(): void
    {
        Assets::addScripts(['jquery-ui'])
            ->addScriptsDirectly([
                'vendor/core/plugins/real-estate/js/global-custom-fields.js',
            ]);

        $this
            ->setupModel(new CustomField())
            ->setValidatorClass(CustomFieldRequest::class)
            ->add('name', TextField::class, NameFieldOption::make()->required()->toArray())
            ->add('type', 'customSelect', [
                'label' => trans('plugins/real-estate::custom-fields.type'),
                'required' => true,
                'attr' => ['class' => 'form-control custom-field-type'],
                'choices' => CustomFieldEnum::labels(),
            ])
            ->setBreakFieldPoint('type')
            ->addMetaBoxes([
                'custom_fields_box' => [
                    'attributes' => [
                        'id' => 'custom_fields_box',
                        'style' => 'display: none;',
                    ],
                    'id' => 'custom_fields_box',
                    'title' => trans('plugins/real-estate::custom-fields.options'),
                    'content' => view(
                        'plugins/real-estate::custom-fields.options',
                        ['options' => $this->model->options->sortBy('order')]
                    )->render(),
                    'header_actions' => Blade::render(sprintf(
                        '<x-core::button id="add-new-row">%s</x-core::button>',
                        trans('plugins/real-estate::custom-fields.option.add_row')
                    )),
                    'has_table' => true,
                ],
            ]);
    }
}
