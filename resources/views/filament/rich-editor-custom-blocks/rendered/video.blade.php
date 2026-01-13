@props(['url' => '', 'caption' => ''])

@php
    $embedVideo = new \Digitonic\FilamentRichEditorTools\Support\EmbeddableVideo($url);
    $embedUrl = $embedVideo->getEmbedUrl();
@endphp

@if(!empty($embedUrl))
<div class="relative mx-auto h-96">
    <iframe
        class="w-full aspect-video"
        width="560"
        height="315"
        src="{{ $embedUrl }}"
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
@else
<div class="relative mx-auto h-96 flex items-center justify-center bg-gray-100 dark:bg-gray-800">
    <p class="text-gray-500 dark:text-gray-400">Video URL not available or invalid: {{ $url }}</p>
</div>
@endif
