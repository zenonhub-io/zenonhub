<footer class="footer pb-3">
    <div class="container">
        <div class="d-md-flex justify-content-sm-between align-items-sm-center text-center">
            <div class="d-block d-md-flex align-items-md-center order-md-1">
                <button type="button" class="btn btn-sm btn-primary order-md-1" data-bs-toggle="modal" data-bs-target="#info-modal">
                    <i class="bi-heart-fill me-2"></i> Donate
                </button>
                <div class="d-block d-md-inline my-3 my-md-0">
                    <a target="_blank" href="https://github.com/zenonhub-io" class="me-4">
                        <i class="bi-github" style="font-size: 1.35rem;"></i>
                    </a>
                    <a target="_blank" href="https://twitter.com/zenonhub" class="me-4">
                        <i class="bi-twitter" style="font-size: 1.35rem;"></i>
                    </a>
                    <a target="_blank" href="https://discordapp.com/users/638703929965674496" class="me-4">
                        <i class="bi-discord" style="font-size: 1.35rem;"></i>
                    </a>
                    <a target="_blank" href="https://t.me/digitalSloth" class="me-4">
                        <i class="bi-telegram" style="font-size: 1.35rem;"></i>
                    </a>
                    <a target="_blank" href="mailto:digitals1oth@proton.me" class="me-0 me-md-4">
                        <i class="bi-envelope" style="font-size: 1.35rem;"></i>
                    </a>
                </div>
            </div>
            <div>
                <span class="">Synced momentum height:</span>
                <strong>
                    <span class="text-primary">{{ number_format(\App\Models\Nom\Momentum::max('height')) }}</span>
                </strong>
            </div>
        </div>
    </div>
</footer>

@include('modals/info')
@include('modals/privacy')


