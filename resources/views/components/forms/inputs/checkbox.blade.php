@php($uuid = Str::random(8))
@php($isError = false)
@error($name)
    @php($isError = true)
@enderror

<div {{ $attributes->class(['form-check', 'form-switch' => $switch]) }}>
    <input class="form-check-input {{ ($isError ? 'is-invalid' : null) }}"
        type="checkbox"
        name="{{ $name }}"
        value="{{ $value }}"
        id="checkbox-{{ $uuid }}"
        @checked($checked)
    >
    <label class="form-check-label {{ ($switch ? 'ms-2' : '') }}" for="checkbox-{{ $uuid }}">
        {{ $slot->isEmpty() ? $label : $slot }}
    </label>
    @error($name)
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror
</div>


