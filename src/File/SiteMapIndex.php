<?php

namespace Robier\Sitemaps\File;

class SiteMapIndex implements Contract
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
        if (null === $this->fullPath) {
            $this->fullPath = $this->path() . $this->name();
        }

        return $this->fullPath;
    }

    public function url(): string
    {
        return $this->url;
    }

    public function fullUrl(): string
    {
        if (null === $this->fullUrl) {
            $this->fullUrl = $this->url() . $this->name();
        }

        return $this->fullUrl;
    }

    public function changeName(string $name): Contract
    {
        return new static($this->path(), $this->url(), $name);
    }
}
