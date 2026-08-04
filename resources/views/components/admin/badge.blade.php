@props([
'type'=>'primary'
])

@php

$color = match($type){

'success'=>'bg-green-100 text-green-700',

'danger'=>'bg-red-100 text-red-700',

'warning'=>'bg-amber-100 text-amber-700',

'info'=>'bg-sky-100 text-sky-700',

default=>'bg-blue-100 text-blue-700'

};

@endphp

<span
class="{{ $color }} px-3 py-1 rounded-full text-xs font-semibold">

{{ $slot }}

</span>
