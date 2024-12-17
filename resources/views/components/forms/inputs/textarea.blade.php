@php($isError = false)
@error($name)
    @php($isError = true)
@enderror

<textarea {{ $attributes->class(['form-control', 'is-invalid' => $isError]) }}
          name="{{ $name }}"
          id="{{ $id }}"
          rows="{{ $rows }}"
>{{ old($name, $slot) }}</textarea>

@error($name)
    <div class="invalid-feedback">
        {{ $message }}
    </div>
@enderror
