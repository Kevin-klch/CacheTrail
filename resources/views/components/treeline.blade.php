{{-- Forest silhouette on top of the footer. public/images/forest.svg holds the forest twice, the second
     copy mirrored, so it repeats seamlessly in any width. --}}
<div {{ $attributes->class(['h-28 bg-bottom-left bg-repeat-x sm:h-36 lg:h-44']) }}
    style="background-image: url('{{ asset('images/forest.svg') }}?v={{ filemtime(public_path('images/forest.svg')) }}'); background-size: auto 100%;" aria-hidden="true"></div>
