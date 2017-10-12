<?php

namespace Robier\Sitemaps\File;

class SiteMap implements Contract
{
    protected $group;
    protected $lastModified;

    protected $data;

    /**
     * Item constructor.
     *
     * @param string      $group
     * @param string      $path
     * @param string      $url
     * @param string      $name
     * @param string|null $lastModified
     */
    public function __construct(string $group, string $path, string $url, string $name, string $lastModified = null)
    {
        $this->group = $group;
        $this->lastModified = $lastModified;

        // @todo make a trait
        $this->data = new SiteMapIndex($path, $url, $name);
    }

    /**
     * @return null|string
     */
    public function lastModified(): ?string
    {
        return $this->lastModified;
    }

    public function group(): string
    {
        return $this->group;
    }

    public function __clone()
    {
        $this->data = clone $this->data;
    }

    /**
     * @return string
     */
    public function url(): string
    {
        return $this->data->url();
    }

    public function fullUrl(): string
    {
        return $this->data->fullUrl();
    }

    public function path(): string
    {
        return $this->data->path();
    }

    public function fullPath(): string
    {
        return $this->data->fullPath();
    }

    public function name(): string
    {
        return $this->data->name();
    }

    public function changeName(string $name): Contract
    {
        return new static($this->group(), $this->path(), $this->url(), $name, $this->lastModified());
    }
}
