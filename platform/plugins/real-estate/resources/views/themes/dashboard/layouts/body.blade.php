<header class="header--mobile">
    <div class="header__left">
        <button class="navbar-toggler">
            <span class="navbar-toggler-icon"></span>
        </button>
    </div>
    <div class="header__center">
        <a class="ps-logo" href="{{ route('public.account.dashboard') }}">
            @if ($logo = theme_option('logo', theme_option('logo')))
                <img
                    src="{{ RvMedia::getImageUrl($logo) }}"
                    alt="{{ theme_option('site_title') }}"
                >
            @endif
        </a>
    </div>
    <div class="header__right">
        <a
            href="{{ route('public.account.logout') }}"
            title="{{ trans('plugins/real-estate::dashboard.header_logout_link') }}"
            onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
        >
            <x-core::icon name="ti ti-logout" />
        </a>

        <form id="logout-form" style="display: none;" action="{{ route('public.account.logout') }}" method="POST">
            @csrf
        </form>
    </div>
</header>
<aside class="ps-drawer--mobile">
    <div class="ps-drawer__header py-3">
        <h4 class="fs-3 mb-0">Menu</h4>
        <button class="ps-drawer__close">
            <x-core::icon name="ti ti-x" />
        </button>
    </div>
    <div class="ps-drawer__content">
        @include('plugins/real-estate::themes.dashboard.layouts.menu')
    </div>
</aside>

<div class="ps-site-overlay"></div>

<main class="ps-main">
    <div class="ps-main__sidebar">
        <div class="ps-sidebar">
            <div class="ps-sidebar__top">
                <div class="ps-block--user-wellcome">
                    <div class="ps-block__left">
                        <img
                            src="{{ auth('account')->user()->avatar_url }}"
                            alt="{{ auth('account')->user()->name }}"
                            class="avatar avatar-lg"
                        />
                    </div>
                    <div class="ps-block__right">
                        <p>{{ __('Hello') }}, {{ auth('account')->user()->name }}</p>
                        <small>{{ __('Joined on :date', ['date' => auth('account')->user()->created_at->translatedFormat('M d, Y')]) }}</small>
                    </div>
                    <div class="ps-block__action">
                        <a
                            href="{{ route('public.account.logout') }}"
                            title="{{ trans('plugins/real-estate::dashboard.header_logout_link') }}"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                        >
                            <x-core::icon name="ti ti-logout" />
                        </a>
                    </div>
                </div>

                <div class="ps-block--earning-count">
                    <small>{{ __('Credits') }}</small>
                    <h3 class="my-2">{{ number_format(auth('account')->user()->credits) }}</h3>
                    <a href="{{ route('public.account.packages') }}" target="_blank">
                        {{ __('Buy credits') }}
                    </a>
                </div>
            </div>
            <div class="ps-sidebar__content">
                <div class="ps-sidebar__center">
                    @include('plugins/real-estate::themes.dashboard.layouts.menu')
                </div>
                <div class="ps-sidebar__footer">
                    <div class="ps-copyright">
                        @php $logo = theme_option('logo', theme_option('logo')); @endphp
                        @if ($logo)
                            <img
                                src="{{ RvMedia::getImageUrl($logo) }}"
                                alt="{{ theme_option('site_title') }}"
                                height="40"
                            >
                        @endif
                        <p>{!! BaseHelper::clean(theme_option('copyright')) !!}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div
        class="ps-main__wrapper"
        id="vendor-dashboard"
    >
        <header class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="fs-1">{{ PageTitle::getTitle(false) }}</h3>

            @if (auth('account')->user()->store && auth('account')->user()->id)
                <div class="d-flex align-items-center gap-4">
                    @if (is_plugin_active('language'))
                        @include(MarketplaceHelper::viewPath('vendor-dashboard.partials.language-switcher'))
                    @endif
                    <a href="{{ auth('account')->user()->url }}" target="_blank" class="d-flex align-items-center gap-2 text-uppercase">
                        {{ __('View your store') }}
                        <i class="icon-exit-right"></i>
                    </a>
                </div>
            @endif
        </header>

        <div id="app">
            @if (auth('account')->check() && !auth('account')->user()->canPost())
                <x-core::alert :title="trans('plugins/real-estate::package.add_credit_warning')">
                    <a href="{{ route('public.account.packages') }}">
                        {{ trans('plugins/real-estate::package.add_credit') }}
                    </a>
                </x-core::alert>
            @endif

            @yield('content')
        </div>
    </div>
</main>
