@props([
    'modelId',
    'modelClass'
])
@php
    $model = $modelClass::withoutGlobalScopes([
        \App\Models\Scopes\DraftedScope::class
    ])->find($modelId);

    $toc = $article
        ? \Filament\Forms\Components\RichEditor\RichContentRenderer::make($article->raw_content)->toTableOfContents()
        : [];

    /**
     * Render the table of contents recursively.
     *
     * @param array<int, array{id:string,text:string,depth:int,subs?:array<mixed>}> $items
     */
    $renderToc = function (array $items) use (&$renderToc): string {
        $html = '<ul class="space-y-1">';

        foreach ($items as $item) {
            $depth = $item['depth'] ?? 0;
            $indentClasses = match ($depth) {
                0 => 'pl-0',
                1 => 'pl-3',
                2 => 'pl-6',
                3 => 'pl-9',
                default => 'pl-12',
            };

            $html .= '<li class="group border-l-2 border-transparent '.$indentClasses.' hover:border-primary-500 transition-all">';
            $html .= '<a href="#'.e($item['id'] ?? '').'" class="block text-sm leading-snug text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 transition-colors">'
                .e($item['text'] ?? '')
                .'</a>';

            if (! empty($item['subs']) && is_array($item['subs'])) {
                $html .= '<div class="mt-1">'.$renderToc($item['subs']).'</div>';
            }

            $html .= '</li>';
        }

        $html .= '</ul>';

        return $html;
    };
@endphp
@if($article && !empty($toc))
    <nav aria-label="Table of contents" class="not-prose rounded-md bg-gray-50 dark:bg-gray-800/40 p-4 ring-1 ring-gray-200 dark:ring-gray-700">
        <h4 class="text-xs font-semibold tracking-wide text-gray-600 dark:text-gray-400 uppercase mb-3">On this page</h4>
        <div class="toc-content text-sm">
            {!! $renderToc($toc) !!}
        </div>
    </nav>
@endif
