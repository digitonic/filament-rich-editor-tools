<div class="inline-flex flex-wrap items-center justify-start gap-4 my-4">
    <a
        style="{{ !empty($background_color) ? 'background: ' . $background_color . ';' : '' }}
            {{ !empty($text_color) ? 'color: ' . $text_color . ';' : 'color: #292F3A; !important' }}
       "
        class="bg-primary-500 hover:bg-primary-300 focus:ring-4 focus:ring-primary-300 rounded-lg text-sm px-5 py-2.5 dark:bg-primary-600 dark:hover:bg-primary-700 focus:outline-hidden dark:focus:ring-primary-800"
        href="{{ $url }}">
        {{ $button_text ?? 'Click' }}
    </a>
</div>
