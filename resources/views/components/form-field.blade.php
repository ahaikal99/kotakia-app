@props(['name', 'label', 'type' => 'text', 'value' => '', 'required' => false, 'hint' => null])
<div>
    <label for="{{ $name }}" class="block text-sm font-semibold mb-2">{{ $label }} @if ($required)<span class="text-[#92743e]" aria-hidden="true">*</span>@endif</label>
    <div class="relative">
    <input id="{{ $name }}" name="{{ $name }}" type="{{ $type }}"
        @if ($type !== 'password') value="{{ old($name, $value) }}" @endif
        @required($required) @error($name) aria-invalid="true" aria-describedby="{{ $name }}-error" @else @if($hint) aria-describedby="{{ $name }}-hint" @endif @enderror
        {{ $attributes->class(['form-input', '!pr-24' => $type === 'password']) }}>
    @if ($type === 'password')
        <button type="button" data-password-toggle="{{ $name }}" aria-controls="{{ $name }}" aria-pressed="false" aria-label="Papar {{ strtolower($label) }}" class="absolute right-2 top-1/2 -translate-y-1/2 rounded-lg px-3 py-2 text-xs font-semibold text-[#92743e] hover:bg-slate-100">Papar</button>
    @endif
    </div>
    @if ($hint)<p id="{{ $name }}-hint" class="text-xs text-slate-500 mt-2">{{ $hint }}</p>@endif
    @error($name)<p id="{{ $name }}-error" class="text-sm text-red-700 mt-2">{{ $message }}</p>@enderror
</div>

