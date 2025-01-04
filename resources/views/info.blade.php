<x-app-layout>
    <x-includes.header :responsive-border="false">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="ls-tight text-wrap text-break">{{ __('About us') }}</h1>
            <div class="d-flex justify-content-center gap-6">
                <a target="_blank" href="https://github.com/zenonhub-io">
                    <i class="bi-github" style="font-size: 1.35rem;"></i>
                </a>
                <a target="_blank" href="https://twitter.com/zenonhub">
                    <i class="bi-twitter-x" style="font-size: 1.35rem;"></i>
                </a>
                <a target="_blank" href="https://t.me/digitalSloth">
                    <i class="bi-telegram" style="font-size: 1.35rem;"></i>
                </a>
            </div>
        </div>
    </x-includes.header>

    <div class="container-fluid px-3 px-md-6 mb-6">
        <p class="mb-3">
            Zenon Hub is a community-driven project supported by Accelerator-Z. Our mission is to provide a reliable and feature-rich blockchain explorer for the Zenon Network, built by the community, for the community.
        </p>
        <p class="mb-3">
            We are dedicated to continuously building, developing, and enhancing the platform to meet the evolving needs of Zenon enthusiasts worldwide, ensuring the best possible experience for our community.
        </p>
        <p class="mb-3">
            Our codebase is open source, reflecting our commitment to transparency and collaboration. We warmly welcome contributions from the community—whether it’s through code, feedback, or ideas—to help improve and expand Zenon Hub.
        </p>
    </div>

</x-app-layout>
