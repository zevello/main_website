<?php

namespace Botble\RealEstate\Forms;

use Botble\Base\Forms\FieldOptions\NameFieldOption;
use Botble\Base\Forms\FieldOptions\StatusFieldOption;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\Fields\TextField;
use Botble\Base\Forms\FormAbstract;
use Botble\RealEstate\Http\Requests\InvestorRequest;
use Botble\RealEstate\Models\Investor;

class InvestorForm extends FormAbstract
{
    public function setup(): void
    {
        $this
            ->setupModel(new Investor())
            ->setValidatorClass(InvestorRequest::class)
            ->add('name', TextField::class, NameFieldOption::make()->required()->toArray())
            ->add('status', SelectField::class, StatusFieldOption::make()->toArray())
            ->setBreakFieldPoint('status');
    }
}
