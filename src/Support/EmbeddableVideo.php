<?php

namespace Digitonic\FilamentRichEditorTools\Support;

class EmbeddableVideo
{
    /**
     * @param  mixed[]  $queryParams
     */
    public function __construct(private string $videoUrl, private array $queryParams = []) {}

    /**
     * @param  mixed[]  $queryParams
     * @return void
     */
    public function setQueryParams(array $queryParams)
    {
        $this->queryParams = $queryParams;
    }

    public function isEmbeddable(): bool
    {
        return $this->isYoutube() || $this->isVimeo();
    }

    public function getEmbedUrl(): string
    {
        if ($this->isEmbeddable()) {
            if ($this->isYoutube()) {
                return $this->youtubeEmbedUrl();
            }

            if ($this->isVimeo()) {
                return $this->vimeoEmbedUrl();
            }
        }

        return '';
    }

    private function isYoutube(): bool
    {
        return str_contains($this->videoUrl, 'youtube.com') || str_contains($this->videoUrl, 'youtu.be');
    }

    private function isVimeo(): bool
    {
        return str_contains($this->videoUrl, 'vimeo.com');
    }

    private function youtubeEmbedUrl(): string
    {
        $url = parse_url($this->videoUrl);

        if (isset($url['query'])) {
            parse_str($url['query'], $query);
            $queryParams = http_build_query(array_merge($this->queryParams, $query));

            if (isset($query['v'])) {
                return 'https://www.youtube.com/embed/'.(is_string($query['v']) ? $query['v'] : '').'?'.$queryParams;
            }
        }

        if (isset($url['path'])) {
            $path = explode('/', $url['path']);

            if (isset($path[1])) {
                return 'https://www.youtube.com/embed/'.$path[1].'?'.http_build_query($this->queryParams);
            }
        }

        return '';
    }

    private function vimeoEmbedUrl(): string
    {
        $url = parse_url($this->videoUrl);

        if (isset($url['path'])) {
            $path = explode('/', $url['path']);

            if (isset($path[1])) {
                return 'https://player.vimeo.com/video/'.$path[1];
            }
        }

        return '';
    }
}
