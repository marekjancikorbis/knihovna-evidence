@props(['name', 'label', 'value' => null, 'type' => 'text', 'hint' => null])
@php
    $invalid = $errors->has($name);
    $describedBy = collect([$hint ? "$name-hint" : null, $invalid ? "$name-error" : null])->filter()->implode(' ');
@endphp
<div class="field">
    <label for="{{ $name }}">{{ $label }}</label>
    <input id="{{ $name }}" name="{{ $name }}" type="{{ $type }}" value="{{ old($name, $value) }}"
           @if ($invalid) aria-invalid="true" @endif
           @if ($describedBy) aria-describedby="{{ $describedBy }}" @endif
           {{ $attributes }}>
    @if ($hint)
        <div class="hint" id="{{ $name }}-hint">{{ $hint }}</div>
    @endif
    @error($name)
        <div class="error" id="{{ $name }}-error"><x-icon name="alert" :size="14" /> {{ $message }}</div>
    @enderror
</div>
