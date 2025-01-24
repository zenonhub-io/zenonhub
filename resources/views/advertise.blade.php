<x-app-layout>
    <x-includes.header :title="__('Advertise with us')" />

    <div class="container-fluid px-3 px-md-6 mb-6">
        <p class="mb-3">
            Zenon Hub offers exclusive advertising opportunities designed to ensure optimal visibility and brand promotion for our sponsors & partners. By securing advertising space, your brand benefits from a strategic placement that maximizes its impact on our audience.
        </p>

        <p class="mb-4">
            If you are interested in advertising with us or want to know more please get in touch through any of the following channels:
        </p>

        <div class="d-flex justify-content-center gap-6">
            <a target="_blank" href="{{ config('zenon-hub.socials.x') }}">
                <i class="bi-twitter-x" style="font-size: 1.4rem;"></i>
            </a>
            <a target="_blank" href="{{ config('zenon-hub.socials.telegram') }}">
                <i class="bi-telegram" style="font-size: 1.4rem;"></i>
            </a>
            <a target="_blank" href="mailto:{{ config('zenon-hub.socials.email') }}">
                <i class="bi-envelope-fill" style="font-size: 1.4rem;"></i>
            </a>
        </div>

{{--        <hr class="my-6" />--}}

{{--        <h3 class="mb-2">Advertisement Types</h3>--}}
{{--        <p>Currently, advertisements are placed exclusively in the sidebar. This approach provides high visibility to users, ensuring that your brand effectively captures attention.</p>--}}
    </div>
</x-app-layout>
