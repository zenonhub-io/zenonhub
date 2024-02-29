<div class="dropdown">
    <button type="button" {{ $attributes->merge(['class' => 'btn btn-secondary dropdown-toggle']) }}
            data-bs-toggle="dropdown"
            aria-expanded="false">
        {{ $trigger }}
    </button>
    <ul class="dropdown-menu">
        {{ $slot }}

        <li><a class="dropdown-item" href="#">Action</a></li>
        <li><a class="dropdown-item" href="#">Another action</a></li>
        <li><a class="dropdown-item" href="#">Something else here</a></li>
    </ul>
</div>
