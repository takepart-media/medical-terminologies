@php
    $user = filament()->auth()->user();
@endphp

<x-filament-widgets::widget class="fi-account-widget h-full" style="height:100%">
    <x-filament::section class="h-full" style="height:100%">
        <x-filament-panels::avatar.user
            size="lg"
            :user="$user"
            loading="lazy"
        />

        <div class="fi-account-widget-main">
            <h2 class="fi-account-widget-heading">
                {{ __('filament-panels::widgets/account-widget.welcome', ['app' => config('app.name')]) }}
            </h2>

            <p class="fi-account-widget-user-name" style="margin-top: 0.2rem">
                {{ filament()->getUserName($user) }}
            </p>

            <p style="margin-top: 0.5rem">
                {{ $user->email }}
            </p>
        </div>

        <form
            action="{{ filament()->getLogoutUrl() }}"
            method="post"
            class="fi-account-widget-logout-form"
        >
            @csrf

            <x-filament::button
                color="gray"
                :icon="\Filament\Support\Icons\Heroicon::ArrowLeftEndOnRectangle"
                :icon-alias="\Filament\View\PanelsIconAlias::WIDGETS_ACCOUNT_LOGOUT_BUTTON"
                labeled-from="sm"
                tag="button"
                type="submit"
            >
                {{ __('filament-panels::widgets/account-widget.actions.logout.label') }}
            </x-filament::button>
        </form>
    </x-filament::section>
</x-filament-widgets::widget>
