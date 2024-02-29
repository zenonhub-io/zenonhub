@php($uuid = Str::random(8))
@php($isError = false)
@error($name)
    @php($isError = true)
@enderror

<div {{ $attributes->merge(['class' => 'form-check']) }}>
    <input class="form-check-input {{ ($isError ? 'is-invalid' : null) }}"
        type="radio"
        name="{{ $name }}"
        value="{{ $value }}"
        id="radio-{{ $uuid }}"
        @selected($selected)
    >
    <label class="form-check-label" for="radio-{{ $uuid }}">
        {{ $slot->isEmpty() ? $label : $slot }}
    </label>
    @error($name)
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror
</div>
