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
                        <div class="progress bg-dark" style="height: 4px">
                            <div
                                class="progress-bar bg-success"
                                role="progressbar"
                                aria-label="No"
                                style="width: {{ $percentageUsed }}%"
                                aria-valuenow="{{ $percentageUsed }}"
                                aria-valuemin="0"
                                aria-valuemax="100"
                            ></div>
                            <div
                                class="progress-bar bg-tertiary"
                                role="progressbar"
                                aria-label="Yes"
                                style="width: {{ $percentageAvailable }}%"
                                aria-valuenow="{{ $percentageAvailable }}"
                                aria-valuemin="0"
                                aria-valuemax="100"
                            ></div>
                        </div>
                    </div>
                </div>
            </div>
        </x-cards.body>
    </x-cards.card>

    <hr class="mb-6">

    @if ($account->qsr_balance > 200 * NOM_DECIMALS)
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
                            <input type="radio" class="btn-check @error('plasma')is-invalid @enderror" name="amount" id="amount-low" autocomplete="off" value="low" wire:model="fuseForm.amount" checked>
                            <label class="btn btn-outline-primary" for="amount-low">{{ __('Low') }}</label>

                            <input type="radio" class="btn-check @error('plasma')is-invalid @enderror" name="amount" id="amount-medium" autocomplete="off" value="medium" wire:model="fuseForm.amount">
                            <label class="btn btn-outline-primary" for="amount-medium">{{ __('Medium') }}</label>

                            <input type="radio" class="btn-check @error('plasma')is-invalid @enderror" name="amount" id="amount-high" autocomplete="off" value="high" wire:model="fuseForm.amount">
                            <label class="btn btn-outline-primary" for="amount-high">{{ __('High') }}</label>
                        </div>
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
    @else
        <x-alerts.alert type="info">
            <i class="bi bi-info-circle-fill me-2"></i> The bot has run out of QSR for the next {{ $nextExpiring?->expires_at->diffForHumans(['parts' => 2], true) }}, please check back later
        </x-alerts.alert>
    @endif

</div>
