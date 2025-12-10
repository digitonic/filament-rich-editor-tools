@props(['url', 'caption'])

@php
$embedVideo = new \Digitonic\FilamentRichEditorTools\Support\EmbeddableVideo($url);
@endphp

<div>
    <iframe
        width="560"
        height="315"
        src="{{ $embedVideo->getEmbedUrl() }}"
        title="Video Player"
        frameborder="0"
        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
        referrerpolicy="strict-origin-when-cross-origin"
        allowfullscreen>
    </iframe>

    @if(!empty($caption))
        <small class="mt-2 text-sm text-center text-gray-500 dark:text-gray-400">
            {{ $caption }}
        </small>
    @endif

</div>
