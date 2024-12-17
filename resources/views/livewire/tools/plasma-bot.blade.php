<div>

    <x-cards.card class="my-6">
        <x-cards.body>
            <div class="row gy-4">
                <div class="col-12">
                    <x-stats.mini-stat :title="__('Available QSR')" :centered="true">
                        <span class="text-secondary">
                            {{ ($account->display_qsr_balance ?: '-') }}
                        </span>
                    </x-stats.mini-stat>
                </div>
                <div class="col-12">
                    <x-stats.mini-stat :title="__('Fused QSR')" :centered="true">
                        <span class="text-tertiary">
                            {{ ($account->display_qsr_fused ?: '-') }}
                        </span>
                    </x-stats.mini-stat>
                </div>
                <div class="col-24">
                    <div class="text-start text-md-center">
                        <div class="progress-stacked" style="height: 4px">
                            <div class="progress" role="progressbar"
                                 aria-label="{{ __('Available QSR') }}"
                                 aria-valuenow="{{ $percentageAvailable }}"
                                 aria-valuemin="0"
                                 aria-valuemax="100"
                                 style="width: {{ $percentageAvailable }}%"
                            >
                                <div class="progress-bar bg-secondary"></div>
                            </div>
                            <div class="progress" role="progressbar"
                                 aria-label="{{ __('Fused QSR') }}"
                                 aria-valuenow="{{ $percentageUsed }}"
                                 aria-valuemin="0"
                                 aria-valuemax="100"
                                 style="width: {{ $percentageUsed }}%"
                            >
                                <div class="progress-bar bg-tertiary"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </x-cards.body>
    </x-cards.card>

    <hr class="mb-6">

    @if ($account->qsr_balance > 200 * NOM_DECIMALS)
        <div class="row">
            <div class="col-lg-16">
                <div class="w-100" wire:loading.delay>
                    <x-alerts.alert type="info" class="mb-6" >
                        <i class="bi bi-arrow-repeat spin me-2"></i> {{ __('Processing request...') }}
                    </x-alerts.alert>
                </div>
            </div>
        </div>

        @if ($result === true)
            <x-alerts.alert type="success" class="mb-6">
                <i class="bi bi-check-circle-fill me-2"></i> {{ __('Generating plasma...') }}
                <hr>
                <p class="mb-4">Plasma is being generated for <x-link :href="route('explorer.account.detail', ['address' => $fuseForm['address']])" class="fw-bold">{{ $fuseForm['address'] }}</x-link> please wait a few minutes for it to arrive.</p>
                <p>Your plasma will expire in {{ $expires }} after which you'll be able to fuse some more.</p>
            </x-alerts.alert>
        @else
            @if($result === false)
                <div class="row">
                    <div class="col-lg-16">
                        @if ($message)
                            <x-alerts.alert type="info" class="mb-6">
                                <i class="bi bi-info-circle-fill me-2"></i> {{ __($message) }}
                            </x-alerts.alert>
                        @else
                            <x-alerts.alert type="danger" class="mb-6">
                                <i class="bi bi-exclamation-circle-fill me-2"></i> {{ __('Error fusing, please try again') }}
                            </x-alerts.alert>
                        @endif
                    </div>
                </div>
            @endif

            <form wire:submit="fusePlasma">
                <x-honeypot livewire-model="extraFields" />
                <div class="vstack gap-6">
                    <div class="row align-items-center">
                        @php($uuid = Str::random(8))
                        <div class="col-lg-4">
                            <x-forms.label :label="__('Address')" for="{{ $uuid }}" />
                        </div>
                        <div class="col-lg-12">
                            <x-forms.inputs.input name="fuseForm.address" id="{{ $uuid }}" wire:model="fuseForm.address" />
                        </div>
                    </div>
                    <div class="row align-items-center">
                        @php($uuid = Str::random(8))
                        <div class="col-lg-4">
                            <x-forms.label :label="__('Plasma')" for="{{ $uuid }}" />
                        </div>
                        <div class="col-lg-12">
                            <div class="btn-group w-100" role="group" aria-label="Plasma level selection">
                                <input type="radio" class="btn-check @error('fuseForm.amount')is-invalid @enderror" name="fuseForm.amount" id="amount-low" autocomplete="off" value="low" wire:model="fuseForm.amount" wire:change="setPlasmaLevelInfo" checked>
                                <label class="btn btn-outline-primary" for="amount-low">{{ __('Low') }}</label>

                                <input type="radio" class="btn-check @error('fuseForm.amount')is-invalid @enderror" name="fuseForm.amount" id="amount-medium" autocomplete="off" value="medium" wire:model="fuseForm.amount" wire:change="setPlasmaLevelInfo">
                                <label class="btn btn-outline-primary" for="amount-medium">{{ __('Medium') }}</label>

                                <input type="radio" class="btn-check @error('fuseForm.amount')is-invalid @enderror" name="fuseForm.amount" id="amount-high" autocomplete="off" value="high" wire:model="fuseForm.amount" wire:change="setPlasmaLevelInfo">
                                <label class="btn btn-outline-primary" for="amount-high">{{ __('High') }}</label>
                            </div>
                            <p class="text-muted text-sm mt-1">{{ $plasmaLevelInfo }}</p>
                        </div>
                    </div>
                    <div class="row align-items-center">
                        <div class="col-lg-16">
                            <button type="submit" class="btn w-100 btn-outline-primary">
                                <i class="bi bi-fire me-2"></i>
                                {{ __('Get Plasma') }}
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        @endif
    @else
        <x-alerts.alert type="info">
            <i class="bi bi-info-circle-fill me-2"></i> The bot has run out of QSR for the next {{ $nextExpiring?->expires_at->diffForHumans(['parts' => 2], true) }}, please check back later
        </x-alerts.alert>
    @endif
</div>
