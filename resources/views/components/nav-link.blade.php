@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full px-4 py-3 text-sm font-medium leading-5 text-gray-100 bg-gray-800 rounded-lg shadow-md hover:bg-gray-700 focus:outline-none focus:ring-2 transition duration-150 ease-in-out'
            : 'block w-full px-4 py-3 text-sm font-medium leading-5 text-gray-100 bg-transpraent rounded-lg shadow-sm hover:bg-gray-700 focus:outline-none focus:ring-2 transition duration-150 ease-in-out';

$classes_li = ($active ?? false)
            ? 'w-full bg-gradient-to-r rounded-lg shadow-md border-l-[5px] border-l-[green] border-solid'
            : 'w-full hover:bg-gradient-to-r text-gray-100 rounded-lg';
@endphp

<li class="{{$classes_li}}">
    <a {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
</li>
