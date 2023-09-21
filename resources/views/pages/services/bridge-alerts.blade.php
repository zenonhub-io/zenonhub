<x-layouts.app pageTitle="{{ $meta['title'] }}" pageDescription="{{ $meta['description'] }}">
    <x-slot name="pageBreadcrumbs">
        {{ Breadcrumbs::render('services.bridge-alerts') }}
    </x-slot>
    <div class="container">
        <div class="row">
            <div class="col-24 col-md-16 offset-md-4 mb-4">
                <div class="card shadow mb-4">
                    <div class="card-header text-center">
                        <h4 class="mb-0">Bridge Alerts</h4>
                    </div>
                    <div class="card-body text-center">
                        Our bridge alert service provides real-time notifications for all actions issued to the bridge or liquidity contracts by the admin or guardian addresses. Refer to <a href="https://github.com/zenonhub-io/zenonhub/blob/main/config/bridge-alerts.php" target="_blank">this</a> config file for more details.
                    </div>
                </div>
                <div class="row">
                    <div class="col-24 col-md-12 mb-4">
                        <div class="card card-hover h-100 shadow text-center">
                            <div class="card-body d-flex align-items-center justify-content-center">
                                <div class="d-block">
                                    <a href="https://discord.com/channels/920058192560533504/1148326129174859936" target="_blank" class="stretched-link">
                                        <span class="d-block mb-2">
                                            <i class="bi-discord opacity-70" style="font-size:2.3rem;"></i>
                                        </span>
                                        <h5>Discord</h5>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-24 col-md-12 mb-4">
                        <div class="card card-hover h-100 shadow text-center">
                            <div class="card-body d-flex align-items-center justify-content-center">
                                <div class="d-block">
                                    <a href="https://t.me/zenon_bridge_alerts" target="_blank" class="stretched-link">
                                        <span class="d-block mb-2">
                                            <i class="bi-telegram opacity-70" style="font-size:2.3rem;"></i>
                                        </span>
                                        <h5>Telegram</h5>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
