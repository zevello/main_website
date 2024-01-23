@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <x-core::alert
        type="primary"
        :title="trans('core/base::system.report_description')"
    >
        <x-core::button
            type="button"
            id="btn-report"
            color="info"
            size="sm"
            class="mt-2"
        >
            {{ trans('core/base::system.get_system_report') }}
        </x-core::button>

        <div class="mt-3" id="report-wrapper" style="display: none;">
            <textarea
                name="txt-report"
                id="txt-report"
                class="form-control"
                rows="10"
                spellcheck="false"
                onfocus="this.select()"
            >
                ### {{ trans('core/base::system.system_environment') }}

                - {{ trans('core/base::system.cms_version') }}: {{ get_cms_version() }}
                - {{ trans('core/base::system.framework_version') }}: {{ $systemEnv['version'] }}
                - {{ trans('core/base::system.timezone') }}: {{ $systemEnv['timezone'] }}
                - {{ trans('core/base::system.debug_mode_off') }}: {!! !$systemEnv['debug_mode'] ? '&#10004;' : '&#10008;' !!}
                - {{ trans('core/base::system.storage_dir_writable') }}: {!! $systemEnv['storage_dir_writable'] ? '&#10004;' : '&#10008;' !!}
                - {{ trans('core/base::system.cache_dir_writable') }}: {!! $systemEnv['cache_dir_writable'] ? '&#10004;' : '&#10008;' !!}
                - {{ trans('core/base::system.app_size') }}: {{ $systemEnv['app_size'] }}

                ### {{ trans('core/base::system.server_environment') }}

                - {{ trans('core/base::system.php_version') }}: {{ $serverEnv['version'] . (!$matchPHPRequirement ? '(' . trans('core/base::system.php_version_error', ['version' => $requiredPhpVersion]) . ')' : '') }}
                - {{ trans('core/base::system.memory_limit') }}: {!! $serverEnv['memory_limit'] ?: '&mdash;' !!}
                - {{ trans('core/base::system.max_execution_time') }}: {!! $serverEnv['max_execution_time'] ?: '&mdash;' !!}
                - {{ trans('core/base::system.server_software') }}: {{ $serverEnv['server_software'] }}
                - {{ trans('core/base::system.server_os') }}: {{ $serverEnv['server_os'] }}
                - {{ trans('core/base::system.database') }}: {{ $serverEnv['database_connection_name'] }}
                - {{ trans('core/base::system.ssl_installed') }}: {!! $serverEnv['ssl_installed'] ? '&#10004;' : '&#10008;' !!}
                - {{ trans('core/base::system.cache_driver') }}: {{ $serverEnv['cache_driver'] }}
                - {{ trans('core/base::system.queue_connection') }}: {{ $serverEnv['queue_connection'] }}
                - {{ trans('core/base::system.session_driver') }}: {{ $serverEnv['session_driver'] }}
                - {{ trans('core/base::system.allow_url_fopen_enabled') }}: {!! $serverEnv['allow_url_fopen_enabled'] ? '&#10004;' : '&#10008;' !!}
                - {{ trans('core/base::system.mbstring_ext') }}: {!! $serverEnv['mbstring'] ? '&#10004;' : '&#10008;' !!}
                - {{ trans('core/base::system.openssl_ext') }}: {!! $serverEnv['openssl'] ? '&#10004;' : '&#10008;' !!}
                - {{ trans('core/base::system.pdo_ext') }}: {!! $serverEnv['pdo'] ? '&#10004;' : '&#10008;' !!}
                - {{ trans('core/base::system.curl_ext') }}: {!! $serverEnv['curl'] ? '&#10004;' : '&#10008;' !!}
                - {{ trans('core/base::system.exif_ext') }}: {!! $serverEnv['exif'] ? '&#10004;' : '&#10008;' !!}
                - {{ trans('core/base::system.file_info_ext') }}: {!! $serverEnv['fileinfo'] ? '&#10004;' : '&#10008;' !!}
                - {{ trans('core/base::system.tokenizer_ext') }}: {!! $serverEnv['tokenizer'] ? '&#10004;' : '&#10008;' !!}
                - {{ trans('core/base::system.imagick_or_gd_ext') }}: {!! $serverEnv['imagick_or_gd'] ? '&#10004;' : '&#10008;' !!}
                - {{ trans('core/base::system.zip') }}: {!! $serverEnv['zip'] ? '&#10004;' : '&#10008;' !!}
                - {{ trans('core/base::system.iconv') }}: {!! $serverEnv['iconv'] ? '&#10004;' : '&#10008;' !!}

                ### {{ trans('core/base::system.installed_packages') }}

                @foreach ($packages as $package)
- {{ $package['name'] }} : {{ $package['version'] }}
@endforeach
            </textarea>
            <x-core::button
                type="button"
                id="copy-report"
                color="info"
                size="sm"
                class="mt-2"
            >
                {{ trans('core/base::system.copy_report') }}
            </x-core::button>
        </div>
    </x-core::alert>

    <div class="row">
        <div class="col-sm-8">
            <x-core::card>
                <x-core::card.header>
                    <x-core::card.title>{{ trans('core/base::system.installed_packages') }}</x-core::card.title>
                </x-core::card.header>
                {!! $infoTable->renderTable() !!}
            </x-core::card>
        </div>

        <div class="col-sm-4">
            <x-core::card class="mb-3">
                <x-core::card.header>
                    <x-core::card.title>{{ trans('core/base::system.system_environment') }}</x-core::card.title>
                </x-core::card.header>

                <ul class="list-group list-group-flush">
                    <li class="list-group-item">
                        {{ trans('core/base::system.cms_version') }}: {{ get_cms_version() }}
                    </li>
                    <li class="list-group-item">
                        {{ trans('core/base::system.framework_version') }}: {{ $systemEnv['version'] }}
                    </li>
                    <li class="list-group-item">
                        {{ trans('core/base::system.timezone') }}: {{ $systemEnv['timezone'] }}
                    </li>
                    <li class="list-group-item">
                        {{ trans('core/base::system.debug_mode_off') }}: {{ $statusIcon(!$systemEnv['debug_mode']) }}
                    </li>
                    <li class="list-group-item">
                        {{ trans('core/base::system.storage_dir_writable') }}: {{ $statusIcon($systemEnv['storage_dir_writable']) }}
                    <li class="list-group-item">
                        {{ trans('core/base::system.cache_dir_writable') }}: {{ $statusIcon($systemEnv['cache_dir_writable']) }}
                    </li>
                    <li class="list-group-item">
                        {{ trans('core/base::system.app_size') }}: {{ $systemEnv['app_size'] }}
                    </li>
                </ul>
            </x-core::card>

            <x-core::card>
                <x-core::card.header>
                    <x-core::card.title>{{ trans('core/base::system.server_environment') }}</x-core::card.title>
                </x-core::card.header>

                <ul class="list-group list-group-flush">
                    <li class="list-group-item">
                        {{ trans('core/base::system.php_version') }}: {{ $serverEnv['version'] }}
                        {{ $statusIcon($matchPHPRequirement) }}
                        @if (!$matchPHPRequirement)
                            ({{ trans('core/base::system.php_version_error', ['version' => $requiredPhpVersion]) }})
                        @endif
                    </li>
                    <li class="list-group-item">
                        {{ trans('core/base::system.memory_limit') }}: {!! $serverEnv['memory_limit'] ?: '&mdash;' !!}
                    </li>
                    <li class="list-group-item">
                        {{ trans('core/base::system.max_execution_time') }}:
                        {!! $serverEnv['max_execution_time'] ?: '&mdash;' !!}</li>
                    <li class="list-group-item">
                        {{ trans('core/base::system.server_software') }}:
                        {{ $serverEnv['server_software'] }}
                    </li>
                    <li class="list-group-item">
                        {{ trans('core/base::system.server_os') }}: {{ $serverEnv['server_os'] }}
                    </li>
                    <li class="list-group-item">
                        {{ trans('core/base::system.database') }}:
                        {{ $serverEnv['database_connection_name'] }}
                    </li>
                    <li class="list-group-item">
                        {{ trans('core/base::system.ssl_installed') }}: {{ $statusIcon($serverEnv['ssl_installed']) }}
                    </li>
                    <li class="list-group-item">
                        {{ trans('core/base::system.cache_driver') }}:
                        {{ $serverEnv['cache_driver'] }}
                    </li>
                    <li class="list-group-item">
                        {{ trans('core/base::system.session_driver') }}:
                        {{ $serverEnv['session_driver'] }}
                    </li>
                    <li class="list-group-item">
                        {{ trans('core/base::system.queue_connection') }}:
                        {{ $serverEnv['queue_connection'] }}
                    </li>
                    <li class="list-group-item">
                        {{ trans('core/base::system.allow_url_fopen_enabled') }}: {{ $statusIcon($serverEnv['allow_url_fopen_enabled']) }}
                    </li>
                    <li class="list-group-item">
                        {{ trans('core/base::system.openssl_ext') }}: {{ $statusIcon($serverEnv['openssl']) }}
                    </li>
                    <li class="list-group-item">
                        {{ trans('core/base::system.mbstring_ext') }}: {{ $statusIcon($serverEnv['mbstring']) }}
                    </li>
                    <li class="list-group-item">
                        {{ trans('core/base::system.pdo_ext') }}: {{ $statusIcon($serverEnv['pdo']) }}
                    </li>
                    <li class="list-group-item">
                        {{ trans('core/base::system.curl_ext') }}: {{ $statusIcon($serverEnv['curl']) }}
                    <li class="list-group-item">
                        {{ trans('core/base::system.exif_ext') }}: {{ $statusIcon($serverEnv['exif']) }}
                    <li class="list-group-item">
                        {{ trans('core/base::system.file_info_ext') }}: {{ $statusIcon($serverEnv['fileinfo']) }}
                    </li>
                    <li class="list-group-item">
                        {{ trans('core/base::system.tokenizer_ext') }}: {{ $statusIcon($serverEnv['tokenizer']) }}
                    </li>
                    <li class="list-group-item">
                        {{ trans('core/base::system.imagick_or_gd_ext') }}: {{ $statusIcon($serverEnv['imagick_or_gd']) }}
                    </li>
                    <li class="list-group-item">
                        {{ trans('core/base::system.zip') }}: {{ $statusIcon($serverEnv['zip']) }}
                    <li class="list-group-item">
                        {{ trans('core/base::system.iconv') }}: {{ $statusIcon($serverEnv['iconv']) }}
                    </li>
                </ul>
            </x-core::card>
        </div>
    </div>
@endsection
