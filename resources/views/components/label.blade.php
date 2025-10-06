@props(['value'])

<label {{ $attributes->merge(['class' => 'block mr-4 my-2 font-medium text-sm text-gray-200']) }}>
    {{ $value ?? $slot }} : 
</label>
