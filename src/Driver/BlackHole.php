<?php

namespace Robier\Sitemaps\Driver;

use Robier\Sitemaps\File\SiteMap as SiteMapFile;

class BlackHole extends Base
{
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
    public function write(string $group, \Iterator $items): \Iterator
    {
        $index = 0;

        $path = $this->name($this->path, $group, $index, $items);
        $generator = $this->chunk($items);

        foreach ($generator->file($path) as $chunk) {
            yield new SiteMapFile(iterator_count($chunk), $group, dirname($path), $this->url, basename($path));

            ++$index;
            $path = $this->name($this->path, $group, $index, $items);
        }
    }
}
