<footer class="footer pb-3">
    <div class="container">
        <div class="d-md-flex justify-content-sm-between align-items-sm-center text-center">
            <div class="d-block d-md-flex align-items-md-center order-md-1">
                <a href="{{ route('services.public-nodes') }}" class="btn btn-sm btn-outline-primary order-md-1 me-2">
                    <i class="bi-hdd-rack-fill me-2"></i> Nodes
                </a>
                <a href="{{ route('donate') }}" class="btn btn-sm btn-outline-primary order-md-1">
                    <i class="bi-heart-fill me-2"></i> Donate
                </a>
                <div class="d-block d-md-inline my-3 my-md-0">
                    <a target="_blank" href="https://github.com/zenonhub-io" class="me-4">
                        <i class="bi-github" style="font-size: 1.35rem;"></i>
                    </a>
                    <a target="_blank" href="https://twitter.com/zenonhub" class="me-4">
                        <i class="bi-twitter-x" style="font-size: 1.35rem;"></i>
                    </a>
                    <a target="_blank" href="https://t.me/digitalSloth" class="me-0 me-md-4">
                        <i class="bi-telegram" style="font-size: 1.35rem;"></i>
                    </a>
                </div>
            </div>
            <div>
                <span class="text-muted">Momentum height:</span>
                <span class="text-primary">{{ number_format(\App\Domains\Nom\Models\Momentum::max('height')) }}</span>
                {{--                <div class="d-block text-muted fs-sm">--}}
                {{--                    <span class="text-break">--}}
                {{--                        {{ config('zenon.public_node_https') }}--}}
                {{--                    </span>--}}
                {{--                    <span class="text-break">--}}
                {{--                        {{ config('zenon.public_node_wss') }}--}}
                {{--                    </span>--}}
                {{--                </div>--}}
            </div>
        </div>
    </div>
</footer>


