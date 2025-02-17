<x-filament-widgets::widget class="fi-account-widget">
    <x-filament::section>
        <div class="flex items-center gap-x-3">

{{--            <span class="fi-avatar object-cover object-center h-10 w-10">--}}
{{--                {!! file_get_contents(public_path("build/svg/logo.svg")) !!}--}}
{{--            </span>--}}

            <div class="flex-1">
                <h2 class="grid flex-1 text-base font-semibold leading-6 text-gray-950 dark:text-white">
                    {{ config('app.name') }}
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    {{ __('Environment') }}:
                    <span @class([
                        'text-blue-500 dark:text-blue-400' => app()->environment('local'),
                        'text-yellow-500 dark:text-yellow-400' => app()->environment('staging'),
                        'text-green-500 dark:text-green-400' => app()->environment('production'),
                    ])>{{ app()->environment() }}</span>
                </p>
            </div>

            <a href="{{ route('home') }}" target="_blank">
                <x-filament::button
                    :href="route('home')"
                    color="gray"
                    icon="heroicon-m-arrow-top-right-on-square"
                    labeled-from="sm"
                    tag="button"
                    type="button"
                >
                    {{ __('Visit') }}
                </x-filament::button>
            </a>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
