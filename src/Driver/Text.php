<?php

namespace Robier\Sitemaps\Driver;

use Robier\Sitemaps\File\SiteMap as SiteMapFile;
use Robier\Sitemaps\Location;

class Text extends Base
{
    const EXTENSION = 'txt';

    const BYTES_PER_FILE = 10485760; // ~10 MB

    public function write(string $group, \Iterator $items): \Iterator
    {
        $index = 0;

        $path = $this->name($this->path, $group, $index, $items);
        $generator = $this->chunk($items);

        foreach ($generator->file($path) as $chunk) {
            $count = $this->writeLinks($path, $chunk);
            yield new SiteMapFile($count, $group, dirname($path), $this->url, basename($path));

            ++$index;
            $path = $this->name($this->path, $group, $index, $items);
        }
    }

    protected function writeLinks($path, $chunks): int
    {
        $handle = fopen($path, 'w');

        /** @var Location $item */
        $count = 0;
        foreach ($chunks as $item) {
            fwrite($handle, $item->url() . PHP_EOL);
            ++$count;
        }

        fclose($handle);

        return $count;
    }
}
