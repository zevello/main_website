<?php

namespace Botble\RealEstate\Http\Controllers\Fronts;

use App\Http\Controllers\Controller;
use Botble\ACL\Traits\SendsPasswordResetEmails;
use Botble\RealEstate\Facades\RealEstateHelper;
use Botble\SeoHelper\Facades\SeoHelper;
use Botble\Theme\Facades\Theme;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    use SendsPasswordResetEmails;

    public function showLinkRequestForm()
    {
        if (! RealEstateHelper::isLoginEnabled()) {
            abort(404);
        }

        SeoHelper::setTitle(trans('plugins/real-estate::account.forgot_password'));

        if (view()->exists(Theme::getThemeNamespace() . '::views.real-estate.account.auth.passwords.email')) {
            return Theme::scope('real-estate.account.auth.passwords.email')->render();
        }

        return view('plugins/real-estate::account.auth.passwords.email');
    }

    public function broker()
    {
        return Password::broker('accounts');
    }
}
