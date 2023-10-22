<x-layouts.app>
    <div class="container">
        <div class="row">
            <div class="col-24 col-md-16 offset-md-4 mb-4">
                <div class="card shadow text-center">
                    <div class="card-header">
                        <h4 class="mb-0">Donate to Zenon Hub</h4>
                    </div>
                    <div class="card-body">
                        <div class="d-block">
                            <p class="mb-4">Your donation goes towards the ongoing costs of developing and maintaining Zenon Hub</p>
                            <span class="text-primary text-break fs-5 d-block user-select-all">
                                {{ config('zenon.donation_address') }} <i class="bi bi-clipboard ms-1 hover-text js-copy" data-clipboard-text="{{ config('zenon.donation_address') }}" data-bs-toggle="tooltip" data-bs-title="Copy"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-24 col-md-16 offset-md-4 mb-4">
                <div class="card shadow">
                    <div class="card-header border-bottom-0 text-center">
                        <h4 class="mb-0">Recent supporters</h4>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-nowrap table-striped table-hover top-border">
                            <tbody>
                            @foreach($donations as $donation)
                                <tr>
                                    <td>
                                        <div class="text-start">
                                                <span class="d-block">
                                                    <x-address :eitherSide="8" breakpoint="lg" :account="$donation->account"/>
                                                </span>
                                            <div class="d-block text-muted">
                                                {{ $donation->display_amount }} {{ $donation->token->symbol }}
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-end">
                                            <span class="d-block">
                                                {{ $donation->created_at->format(config('zenon.short_date_format')) }}
                                            </span>
                                            <span class="d-block text-muted fs-sm">
                                                {{ now()->subSeconds(now()->timestamp - $donation->created_at->timestamp)->diffForHumans(['parts' => 2]) }}
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
