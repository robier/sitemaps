<?php

namespace Robier\Sitemaps\File;

use DateTimeInterface;

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
     * @param string|DateTimeInterface $lastModified
     */
    public function __construct(string $group, string $path, string $url, string $name, DateTimeInterface $lastModified = null)
    {
        $this->group = $group;
        $this->lastModified = $lastModified;

        // @todo make a trait
        $this->data = new SiteMapIndex($path, $url, $name);
    }

    /**
     * @return null|DateTimeInterface
     */
    public function lastModified(): ?DateTimeInterface
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
