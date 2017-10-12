<?php

namespace Robier\Sitemaps\Driver;

use Robier\Sitemaps\File\SiteMap as SiteMapFile;
use Robier\Sitemaps\Location;

class Text extends Base
{
    const EXTENSION = 'txt';

    public function write(string $group, \Iterator $items): \Iterator
    {
        $index = 0;

        $path = $this->name($this->path, $group, $index);
        $generator = $this->chunk($items, $path);

        $index = 0;
        foreach ($generator as $chunk) {
            $this->writeLinks($path, $chunk);
            yield new SiteMapFile($group, dirname($path), $this->url, basename($path));

            ++$index;
            $path = $this->name($this->path, $group, $index);
            $generator->file($path);
        }
    }

    protected function writeLinks($path, $chunks): void
    {
        $handle = fopen($path, 'w');

        /** @var Location $item */
        foreach ($chunks as $item) {
            fwrite($handle, $item->url() . PHP_EOL);
        }

        fclose($handle);
    }
}
