@props(['label', 'name', 'type' => 'text', 'hint' => null])

@php $hasError = $errors->has($name); @endphp

<div>
    <label for="{{ $name }}" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">
        {{ $label }}
    </label>

    <input
        id="{{ $name }}"
        name="{{ $name }}"
        type="{{ $type }}"
        @if ($type !== 'password') value="{{ old($name) }}" @endif
        {{ $attributes->merge([
            'class' => 'block w-full rounded-lg border px-3 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 shadow-sm transition focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600/20 '
                . ($hasError ? 'border-red-400' : 'border-slate-200'),
        ]) }}
    >

    @error($name)
        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
    @else
        @if ($hint)
            <p class="mt-1.5 text-xs text-slate-400">{{ $hint }}</p>
        @endif
    @enderror
</div>
