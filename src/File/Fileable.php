<?php

namespace Robier\Sitemaps\File;

trait Fileable
{
    /**
     * @var string
     */
    protected $path;
    /**
     * @var string
     */
    protected $url;

    protected $name;

    protected $fullPath = null;
    protected $fullUrl = null;

    protected $hasSiteMapIndex = false;

    protected $linksCount;

    public function __construct(int $linksCount, string $path, string $url, string $name)
    {
        $this->linksCount = $linksCount;
        $this->path = rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $this->url = rtrim($url, '/') . '/';
        $this->name = $name;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function path(): string
    {
        return $this->path;
    }

    public function fullPath(): string
    {
        return $this->path() . $this->name();
    }

    public function url(): string
    {
        return $this->url;
    }

    public function fullUrl(): string
    {
        return $this->fullUrl = $this->url() . $this->name();
    }

    public function hasSiteMapIndex(): bool
    {
        return $this->hasSiteMapIndex;
    }

    public function count(): int
    {
        return $this->linksCount;
    }
}
