<x-app-layout>
    <x-includes.header :title="__('Donate to Zenon Hub')" />

    <div class="container-fluid px-3 px-md-6 mb-6">
        <p>
            Thank you for considering a donation to Zenon Hub! Your support helps cover the ongoing costs of development, maintenance, and improvements to ensure our explorer remains reliable, fast, and feature-rich. Every contribution, no matter the size, makes a significant difference and is deeply appreciated. Together, we can continue building a better platform for the community.
        </p>
        <hr>
        <x-cards.card>
            <x-cards.body class="text-center">
                <p class="lead text-primary">
                    {{ config('zenon-hub.donation_address') }} <x-copy :text="config('zenon-hub.donation_address')" class="ms-2" />
                </p>
            </x-cards.body>
        </x-cards.card>
    </div>

    <h2 class="px-3 px-md-6 mb-4">{{ __('Latest supporters') }}</h2>

    <table class="table table-nowrap table-striped border-top">
        <tbody>
        @foreach($donations as $donation)
            <tr>
                <td>
                    <div class="text-start">
                            <span class="d-block">
                                <x-address :account="$donation->account" :eitherSide="8" breakpoint="lg" />
                            </span>
                        <div class="d-block text-muted">
                            {{ $donation->display_amount }} {{ $donation->token->symbol }}
                        </div>
                    </div>
                </td>
                <td>
                    <div class="text-end">
                        <span class="d-block">
                            <x-date-time.carbon :date="$donation->created_at" :show-tooltip="false" />
                        </span>
                        <span class="d-block text-muted fs-sm">
                            <x-date-time.carbon :date="$donation->created_at" :show-tooltip="false" :human="true" />
                        </span>
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</x-app-layout>
