<x-layouts.app>
    <x-slot name="breadcrumbs">
        {{ Breadcrumbs::render('services.whale-alerts') }}
    </x-slot>
    <div class="container">
        <div class="row">
            <div class="col-24 col-md-16 offset-md-4 mb-4">
                <div class="card shadow mb-4">
                    <div class="card-header text-center">
                        <h4 class="mb-0">Whale Alerts</h4>
                    </div>
                    <div class="card-body text-center">
                        Our whale alerts service is accessible across multiple platforms and delivers real-time notifications for transactions greater than {{ $znnCutoff }} ZNN or {{ $qsrCutoff }} QSR.
                    </div>
                </div>
                <div class="row">
                    <div class="col-24 col-md-8 mb-4">
                        <div class="card card-hover h-100 shadow text-center">
                            <div class="card-body d-flex align-items-center justify-content-center">
                                <div class="d-block">
                                    <a href="https://twitter.com/ZenonWhaleAlert" target="_blank" class="stretched-link">
                                        <span class="d-block mb-2">
                                            <i class="bi-twitter opacity-70" style="font-size:2.3rem;"></i>
                                        </span>
                                        <h5>Twitter</h5>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-24 col-md-8 mb-4">
                        <div class="card card-hover h-100 shadow text-center">
                            <div class="card-body d-flex align-items-center justify-content-center">
                                <div class="d-block">
                                    <a href="https://discord.com/channels/920058192560533504/1069947655364542494" target="_blank" class="stretched-link">
                                        <span class="d-block mb-2">
                                            <i class="bi-discord opacity-70" style="font-size:2.3rem;"></i>
                                        </span>
                                        <h5>Discord</h5>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-24 col-md-8 mb-4">
                        <div class="card card-hover h-100 shadow text-center">
                            <div class="card-body d-flex align-items-center justify-content-center">
                                <div class="d-block">
                                    <a href="https://t.me/zenonwhalealerts" target="_blank" class="stretched-link">
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
