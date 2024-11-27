@php($uuid = Str::random(8))

<div {{ $attributes }}>
    <x-forms.label :label="$label" :for="$uuid" />
    <x-forms.inputs.input :name="$name" :type="$type" :id="$uuid" :value="$value "/>
    @if($helpText)
        <div class="form-text">{{ $helpText }}</div>
    @endif
</div>
