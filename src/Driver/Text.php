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
        $generator = $this->chunk($items);

        foreach ($generator->file($path) as $chunk) {
            $count = $this->writeLinks($path, $chunk);
            yield new SiteMapFile($count, $group, dirname($path), $this->url, basename($path));

            ++$index;
            $path = $this->name($this->path, $group, $index);
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
