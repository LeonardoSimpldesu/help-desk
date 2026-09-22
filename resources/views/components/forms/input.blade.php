@props(['label', 'name', 'type' => 'text'])

@php $hasError = $errors->has($name); @endphp

<div>
    <label for="{{ $name }}" class="mb-1.5 block text-sm font-medium text-slate-700">
        {{ $label }}
    </label>

    <input
        id="{{ $name }}"
        name="{{ $name }}"
        type="{{ $type }}"
        @if ($type !== 'password') value="{{ old($name) }}" @endif
        {{ $attributes->merge([
            'class' => 'block w-full rounded-lg border px-3 py-2 text-sm text-slate-900 shadow-sm transition focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/40 '
                . ($hasError ? 'border-red-400' : 'border-slate-300'),
        ]) }}
    >

    @error($name)
        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
