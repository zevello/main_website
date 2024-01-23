<form action="{{ $actionUrl ?? RealEstateHelper::getProjectsListPageUrl() }}" data-ajax-url="{{ $ajaxUrl ?? route('public.projects') }}" class="search-filter">
    <div class="space-y-5 registration-form text-dark text-start">
        <div class="grid grid-cols-1 gap-6 md:grid-cols-3 lg:gap-0">
            {!! Theme::partial('filters.keyword', compact('type')) !!}

            {!! Theme::partial('filters.location', compact('type')) !!}

            {!! Theme::partial('filters.category', compact('id', 'type', 'categories')) !!}
        </div>

        <button type="button" class="flex items-center gap-2 toggle-advanced-search text-primary hover:text-secondary">
            {{ __('Advanced') }}
            <i class="mdi mdi-chevron-down-circle-outline"></i>
        </button>

        <div class="hidden space-y-5 transition-all duration-200 ease-in-out advanced-search">
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4 lg:gap-0">
                {!! Theme::partial('filters.block', compact('id', 'type')) !!}

                {!! Theme::partial('filters.floor', compact('id', 'type')) !!}

                {!! Theme::partial('filters.flat', compact('id', 'type')) !!}
            </div>
        </div>

        <div class="grid items-center grid-cols-3 gap-2 md:flex">
            <button type="submit" class="col-span-2 btn bg-primary hover:bg-secondary border-primary hover:border-secondary text-white submit-btn w-full md:w-1/4 !h-12 rounded transition-all ease-in-out duration-200">
                <i class="mdi mdi-magnify me-2"></i>
                {{ __('Search') }}
            </button>

            <button type="button" class="col-span-1 md:mt-0 block md:inline-block w-full md:w-fit px-4 bg-slate-500 rounded text-white py-[0.70rem] hover:bg-slate-600 reset-filter">
                <i class="me-1 mdi mdi-refresh"></i>
                {{ __('Reset') }}
            </button>
        </div>
    </div>
</form>
