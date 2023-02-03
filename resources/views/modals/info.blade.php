<div class="modal mod-modal fade" id="info-modal" tabindex="-1" aria-labelledby="infoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-bottom">
                <h5 class="modal-title" id="infoModalLabel">{{ config('app.name') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-block mb-4">
                    <h6 class="mb-2">About us</h6>
                    <p>{{ config('app.name') }} is an explorer for the Zenon Network and provides a range of tools for interacting with and building on-top of the Network of Momentum.</p>
                </div>
                <div class="d-block mb-4">
                    <h6 class="mb-2">Donation address</h6>
                    <span class="text-primary fw-bold text-break">
                        {{ config('zenon.donation_address') }}
                    </span>
                    <p class="mt-2">We are a community project and your donation goes towards the ongoing maintenance and development costs, thanks for your support.</p>
                </div>
                <div class="d-block mb-4">
                    <h6 class="mb-2">Public nodes</h6>
                    <p>We offer two secure endpoints, these are available for anyone who wants to connect to the Network of Momentum without running your own node.</p>
                    HTTPS: <span class="text-primary fw-bold text-break">
                        {{ config('zenon.public_node_https') }}
                    </span>
                    <br>
                    WSS: <span class="text-primary fw-bold text-break">
                        {{ config('zenon.public_node_wss') }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
