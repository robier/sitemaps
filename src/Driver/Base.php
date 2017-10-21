<?php

namespace Robier\Sitemaps\Driver;

use Robier\Sitemaps\Iterators\FileChunk;
use Robier\Sitemaps\Location;

abstract class Base implements Contract
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

    protected function name(string $prefix, string $name, string $suffix, \Iterator $items = null): string
    {
        $subGroup = null;
        if (null !== $items) {
            $subGroup = $this->subGroup($items);
        }

        if (null === $subGroup) {
            return sprintf('%s%s-%s.%s', $prefix, $name, $suffix, static::EXTENSION);
        }

        return sprintf('%s%s-%s-%s.%s', $prefix, $name, $suffix, $subGroup, static::EXTENSION);
    }

    protected function subGroup(\Iterator $items): ?string
    {
        if (!$items->valid() || !($items->current() instanceof Location)) {
            return null;
        }

        return $items->current()->subGroup();
    }

    protected function chunk(\Iterator $iterator): FileChunk
    {
        return new FileChunk($iterator, static::ITEMS_PER_FILE, static::BYTES_PER_FILE);
    }
}
