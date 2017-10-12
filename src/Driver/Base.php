<?php

namespace Robier\Sitemaps\Driver;

use Robier\Sitemaps\Iterators\FileChunk;

abstract class Base
{
    const ITEMS_PER_FILE = 50000;
    const BYTES_PER_FILE = 52000000; // ~50MB

    const EXTENSION = null;

    protected $path;
    protected $url;

    public function __construct(string $path, string $url)
    {
        $this->path = rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $this->url = rtrim($url, '/') . '/';

        if (!is_dir($this->path)) {
            throw new \InvalidArgumentException('Path must point to existing directory');
        }
    }

    protected function name(string $prefix, string $name, string $suffix): string
    {
        return sprintf('%s%s-%s.%s', $prefix, $name, $suffix, static::EXTENSION);
    }

    protected function chunk(\Iterator $iterator, string $filePath): FileChunk
    {
        return new FileChunk($iterator, $filePath, static::ITEMS_PER_FILE, static::BYTES_PER_FILE);
    }

    /**
     * Iterator must return instance of file contract
     *
     * @see \Robier\Sitemaps\File\Contract
     *
     * @param string    $group
     * @param \Iterator $items
     *
     * @return \Iterator|\Robier\Sitemaps\File\SiteMap[]|\Robier\Sitemaps\File\SiteMapIndex[]
     */
    abstract public function write(string $group, \Iterator $items): \Iterator;
}
