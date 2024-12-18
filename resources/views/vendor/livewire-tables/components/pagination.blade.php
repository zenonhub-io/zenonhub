@includeWhen(
    $this->hasConfigurableAreaFor('before-pagination'),
    $this->getConfigurableAreaFor('before-pagination'),
    $this->getParametersForConfigurableArea('before-pagination')
)

<div {{ $this->getPaginationWrapperAttributesBag() }}>
    @if ($this->paginationVisibilityIsEnabled())
        @if ($this->isTailwind)
            <div class="mt-4 px-4 md:p-0 sm:flex justify-between items-center space-y-4 sm:space-y-0">
                <div>
                    @if ($this->paginationIsEnabled && $this->isPaginationMethod('standard') && $this->getRows->lastPage() > 1 && $this->showPaginationDetails)
                        <p class="paged-pagination-results text-sm text-gray-700 leading-5 dark:text-white">
                            <span>{{ __($this->getLocalisationPath.'Showing') }}</span>
                            <span class="font-medium">{{ $this->getRows->firstItem() }}</span>
                            <span>{{ __($this->getLocalisationPath.'to') }}</span>
                            <span class="font-medium">{{ $this->getRows->lastItem() }}</span>
                            <span>{{ __($this->getLocalisationPath.'of') }}</span>
                            <span class="font-medium"><span x-text="paginationTotalItemCount"></span></span>
                            <span>{{ __($this->getLocalisationPath.'results') }}</span>
                        </p>
                    @elseif ($this->paginationIsEnabled && $this->isPaginationMethod('simple') && $this->showPaginationDetails)
                        <p class="paged-pagination-results text-sm text-gray-700 leading-5 dark:text-white">
                            <span>{{ __($this->getLocalisationPath.'Showing') }}</span>
                            <span class="font-medium">{{ $this->getRows->firstItem() }}</span>
                            <span>{{ __($this->getLocalisationPath.'to') }}</span>
                            <span class="font-medium">{{ $this->getRows->lastItem() }}</span>
                        </p>
                    @elseif ($this->paginationIsEnabled && $this->isPaginationMethod('cursor'))
                    @else
                        @if($this->showPaginationDetails)
                            <p class="total-pagination-results text-sm text-gray-700 leading-5 dark:text-white">
                                <span>{{ __($this->getLocalisationPath.'Showing') }}</span>
                                <span class="font-medium">{{ $this->getRows->count() }}</span>
                                <span>{{ __($this->getLocalisationPath.'results') }}</span>
                            </p>
                        @endif
                    @endif
                </div>

                @if ($this->paginationIsEnabled)
                    {{ $this->getRows->links('livewire-tables::specific.tailwind.'.(!$this->isPaginationMethod('standard') ? 'simple-' : '').'pagination') }}
                @endif
            </div>
        @else
            @if ($this->paginationIsEnabled && $this->isPaginationMethod('standard') && $this->getRows->lastPage() > 1)
                <div class="row mt-3 d-flex align-items-center">
                    <div class="col-24 col-lg-10 text-center text-lg-start text-muted text-sm mb-3 mb-lg-0">
                        @if($this->showPaginationDetails)
                            <span>@lang('Showing')</span>
                            <strong>{{ $this->getRows->count() ? number_format($this->getRows->firstItem()) : 0 }}</strong>
                            <span>@lang('to')</span>
                            <strong>{{ $this->getRows->count() ? number_format($this->getRows->lastItem()) : 0 }}</strong>
                            <span>@lang('of')</span>
                            <strong><span x-text="paginationTotalItemCount"></span></strong>
                            <span>@lang('results')</span>
                        @endif
                    </div>
                    <div class="col-24 col-lg-14 overflow-auto">
                        {{ $this->getRows->onEachSide(1)->links('livewire-tables::specific.bootstrap-4.pagination') }}
                    </div>
                </div>
            @elseif ($this->paginationIsEnabled && $this->isPaginationMethod('simple'))
                <div class="row mt-3 d-flex align-items-center">
                    <div class="col-24 col-lg-10 text-center text-lg-start text-muted text-sm mb-3 mb-lg-0">
                        @if($this->showPaginationDetails)
                            <span>@lang('Showing')</span>
                            <strong>{{ $this->getRows->count() ? number_format($this->getRows->firstItem()) : 0 }}</strong>
                            <span>@lang('to')</span>
                            <strong>{{ $this->getRows->count() ? number_format($this->getRows->lastItem()) : 0 }}</strong>
                        @endif
                    </div>
                    <div class="col-24 col-lg-14 overflow-auto">
                        {{ $this->getRows->links('livewire-tables::specific.bootstrap-4.simple-pagination') }}
                    </div>
                </div>
            @elseif ($this->paginationIsEnabled && $this->isPaginationMethod('cursor'))
                <div class="row mt-3">
                    <div class="col-24 col-md-12 overflow-auto">
                        {{ $this->getRows->links('livewire-tables::specific.bootstrap-4.simple-pagination') }}
                    </div>
                </div>
            @else
                <div class="row mt-3">
                    <div class="col-12 text-muted">
                        @if($this->showPaginationDetails)
                            {{ __($this->getLocalisationPath.'Showing') }}
                            <strong>{{ $this->getRows->count() }}</strong>
                            {{ __($this->getLocalisationPath.'results') }}
                        @endif
                    </div>
                </div>
            @endif
        @endif
    @endif
</div>

@includeWhen(
    $this->hasConfigurableAreaFor('after-pagination'),
    $this->getConfigurableAreaFor('after-pagination'),
    $this->getParametersForConfigurableArea('after-pagination')
)
