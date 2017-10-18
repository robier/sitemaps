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

    public function __construct(string $path, string $url, string $name)
    {
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
}
