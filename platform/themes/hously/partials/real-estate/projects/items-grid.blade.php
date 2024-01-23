@if($projects->isNotEmpty())
    <div class="grid lg:grid-cols-3 md:grid-cols-2 grid-cols-1 mt-8 gap-[30px]">
        @foreach($projects as $project)
            <div class="overflow-hidden duration-500 ease-in-out bg-white shadow project-item group rounded-xl dark:bg-slate-800 hover:shadow-lg dark:shadow-gray-700 dark:hover:shadow-gray-700">
                <div class="relative overflow-hidden">
                    <a href="{{ $project->url }}">
                        <img src="{{ RvMedia::getImageUrl($project->image, 'small', false, RvMedia::getDefaultImage()) }}" alt="{{ $project->name }}" class="transition-all duration-500 hover:scale-110">
                    </a>
                    <div class="absolute top-6 end-6">
                        <button type="button" class="text-lg text-red-600 bg-white rounded-full shadow btn btn-icon dark:bg-slate-900 dark:shadow-gray-700 add-to-wishlist" aria-label="{{ __('Add to wishlist') }}" data-box-type="project" data-id="{{ $project->id }}">
                            <i class="mdi mdi-heart-outline"></i>
                        </button>
                    </div>
                    @if($project->images && $imagesCount = count($project->images))
                        <div class="absolute top-6 start-6">
                            <div class="flex items-center justify-center p-2 py-1 text-sm text-white bg-gray-700 rounded-lg bg-opacity-30">
                                <i class="mdi mdi-camera-outline me-1"></i>
                                <span>{{ $imagesCount }}</span>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="p-6">
                    <a href="{{ $project->url }}" class="text-lg font-medium uppercase duration-500 ease-in-out hover:text-primary">
                        {!! BaseHelper::clean($project->name) !!}
                    </a>
                    @if($project->city->name || $project->state->name)
                        <p class="truncate text-slate-600 dark:text-slate-300">
                            <i class="mdi mdi-map-marker-outline"></i>
                            {{ $project->city->name ? $project->city->name . ', ' : '' }}{{ $project->state->name }}
                        </p>
                    @endif
                    <div class="flex flex-wrap gap-3 items-center justify-between pt-4 ps-0 mb-0 list-none">
                        @if($project->price_from || $project->price_to)
                            <li>
                                <span class="text-slate-400">{{ __('Price') }}</span>
                                <p class="text-lg font-semibold">{{ __(':from - :to', ['from' => format_price($project->price_from), 'to' => format_price($project->price_to)]) }}</p>
                            </li>
                        @endif

                        @if(RealEstateHelper::isEnabledReview())
                            <li>
                                <span class="text-slate-400">{{ __('Rating') }}</span>
                                @include(Theme::getThemeNamespace('views.real-estate.partials.review-star'), ['avgStar' => $project->reviews_avg_star, 'count' => $project->reviews_count])
                            </li>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="my-16 text-center">
        <svg class="mx-auto h-24 w-24 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 21v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21m0 0h4.5V3.545M12.75 21h7.5V10.75M2.25 21h1.5m18 0h-18M2.25 9l4.5-1.636M18.75 3l-1.5.545m0 6.205l3 1m1.5.5l-1.5-.5M6.75 7.364V3h-3v18m3-13.636l10.5-3.819" />
        </svg>
        <p class="mt-3 text-xl text-gray-500 dark:text-gray-300">{{ __('No projects found.') }}</p>
    </div>
@endif
