@props([
    'affiliates' => [],
    'show_ratings' => false,
    'random' => false,
])

<div id="affiliate-list-content" class="not-prose">
    <div class="mt-5 mb-10">
        <livewire:affiliate-list
            wire:key="affiliate-list={{ md5(json_encode($affiliates)) }}"
            :items="$affiliates"
            :random="$random"
        />
    </div>
</div>
