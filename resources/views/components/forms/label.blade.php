<label for="{{ $for }}" {{ $attributes->merge(['class' => 'form-label']) }}>
    {{ $slot->isEmpty() ? $label : $slot }}
</label>
