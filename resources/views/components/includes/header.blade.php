@props(['title', 'centered' => false, 'responsiveBorder' => false])

<header class="{{ $responsiveBorder ? 'border-bottom-0 border-bottom-md' : 'border-bottom' }} mb-6 mx-3 mx-lg-6">
    <div class="row align-items-center mb-4">
        <div class="col">
            <h1 class="ls-tight {{ $centered ? 'text-center' : null }}">{{ $title }}</h1>
        </div>
    </div>
    {{ $slot }}
</header>
