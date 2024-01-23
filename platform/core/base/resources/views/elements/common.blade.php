<script type="text/javascript">
    var BotbleVariables = BotbleVariables || {};

    @if (Auth::guard()->check())
        BotbleVariables.languages = {
            tables: {{ Js::from(trans('core/base::tables')) }},
            notices_msg: {{ Js::from(trans('core/base::notices')) }},
            pagination: {{ Js::from(trans('pagination')) }},
        };
        BotbleVariables.authorized =
            "{{ setting('membership_authorization_at') && Carbon\Carbon::now()->diffInDays(Carbon\Carbon::createFromFormat('Y-m-d H:i:s', setting('membership_authorization_at'))) <= 7 ? 1 : 0 }}";
    @else
        BotbleVariables.languages = {
            notices_msg: {{ Js::from(trans('core/base::notices')) }},
        };
    @endif
</script>

@push('footer')
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
            })
        </script>
    @endif
@endpush
