@php($isError = false)

@error($name)
    @php($isError = true)
@enderror

<input {{ $attributes->class(['form-control', 'is-invalid' => $isError]) }}
    name="{{ $name }}"
    type="{{ $type }}"
    id="{{ $id }}"
    @if($value)value="{{ $value }}" @endif
    @required($required)
    @readonly($readonly)
    @disabled($readonly)
/>

@error($name)
    <div class="invalid-feedback">
        {{ $message }}
    </div>
@enderror

