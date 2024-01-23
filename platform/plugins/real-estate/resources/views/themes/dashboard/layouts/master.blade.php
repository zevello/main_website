<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="format-detection" content="telephone=no">
        <meta name="apple-mobile-web-app-capable" content="yes">
        @if (theme_option('favicon'))
            <link href="{{ RvMedia::getImageUrl(theme_option('favicon')) }}" rel="shortcut icon">
        @endif

        <meta name="csrf-token" content="{{ csrf_token() }}">

        @include('plugins/real-estate::themes.dashboard.layouts.header')

        <script type="text/javascript">
            window.trans = JSON.parse('{!! addslashes(json_encode(trans('plugins/real-estate::dashboard'))) !!}');
            var BotbleVariables = BotbleVariables || {};
            BotbleVariables.languages = {
                tables: {!! json_encode(trans('core/base::tables'), JSON_HEX_APOS) !!},
                notices_msg: {!! json_encode(trans('core/base::notices'), JSON_HEX_APOS) !!},
                pagination: {!! json_encode(trans('pagination'), JSON_HEX_APOS) !!},
                system: {
                    'character_remain': '{{ trans('core/base::forms.character_remain') }}'
                }
            };
            var RV_MEDIA_URL = {
                'media_upload_from_editor': '{{ route('public.account.upload-from-editor') }}'
            };
        </script>

        {!! apply_filters('account_dashboard_header', null) !!}
        @stack('header')
    </head>

    <body @if (BaseHelper::isRtlEnabled()) dir="rtl" @endif>
        {!! apply_filters('real_estate_dashboard_header', null) !!}

        @yield('body', view('plugins/real-estate::themes.dashboard.layouts.body'))

        @include('plugins/real-estate::themes.dashboard.layouts.footer')
        {!! Assets::renderFooter() !!}
        @stack('scripts')
        @stack('footer')
        {!! apply_filters(THEME_FRONT_FOOTER, null) !!}

        @if (Session::has('success_msg') || Session::has('error_msg') || (isset($errors) && $errors->any()) || isset($error_msg))
            <script type="text/javascript">
                $(function() {
                    @if (Session::has('success_msg'))
                        Botble.showSuccess('{{ session('success_msg') }}');
                    @endif
                    @if (Session::has('error_msg'))
                        Botble.showError('{{ session('error_msg') }}');
                    @endif
                    @if (isset($error_msg))
                        Botble.showError('{{ $error_msg }}');
                    @endif
                    @if (isset($errors))
                    @foreach ($errors->all() as $error)
                            Botble.showError('{{ $error }}');
                        @endforeach
                    @endif
                });
            </script>
        @endif
    </body>
</html>
