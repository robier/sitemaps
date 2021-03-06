<?php

namespace Robier\Sitemaps\File;

use DateTimeInterface;

class SiteMap implements Contract
{
    use Fileable{
        __construct as protected traitConstructor;
    }

    protected $group;
    protected $lastModified;

    /**
     * Item constructor.
     *
     * @param int                      $linksCount
     * @param string                   $group
     * @param string                   $path
     * @param string                   $url
     * @param string                   $name
     * @param string|DateTimeInterface $lastModified
     */
    public function __construct(int $linksCount, string $group, string $path, string $url, string $name, DateTimeInterface $lastModified = null)
    {
        $this->traitConstructor($linksCount, $path, $url, $name);

        $this->group = $group;
        $this->lastModified = $lastModified;
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

    public function changeName(string $name): Contract
    {
        $this->name = $name;

        return $this;
    }

    public function changeSiteMapIndexFlag(bool $bool): Contract
    {
        $this->hasSiteMapIndex = $bool;

        return $this;
    }
}
