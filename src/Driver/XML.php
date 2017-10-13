<?php

namespace Robier\Sitemaps\Driver;

use Robier\Sitemaps\Driver\Writer\XML as XMLWriter;
use Robier\Sitemaps\File\SiteMap as SiteMapFile;
use Robier\Sitemaps\File\SiteMapIndex;

class XML extends Base
{
    const EXTENSION = 'xml';

    const DATE = 'Y-m-d';
    const DATETIME = \DateTime::W3C;

    protected $writer;
    protected $dateFormat;

    public function __construct(string $path, string $url, string $dateFormat = self::DATE)
    {
        parent::__construct($path, $url);

        $this->dateFormat = $dateFormat;

        $this->writer = new XMLWriter();
    }

    public function write(string $group, \Iterator $items): \Iterator
    {
        $siteMaps = $this->siteMap($group, $items);

        return $this->siteMapIndex($group, $siteMaps);
    }

    protected function siteMap(string $group, \Iterator $items): \Generator
    {
        $index = 0;

        $path = $this->name($this->path, $group, $index);
        $generator = $this->chunk($items, $path);

        foreach ($generator as $chunk) {
            $this->writer->writeSiteMap($path, $chunk, $this->dateFormat);
            yield new SiteMapFile($group, dirname($path), $this->url, basename($path));

            ++$index;
            $path = $this->name($this->path, $group, $index);
            $generator->file($path);
        }
    }

    protected function siteMapIndex(string $group, \Iterator $items): \Generator
    {
        $index = 0;

        $path = $this->name($this->path, $group, 'index' . $index);
        $generator = $this->chunk($items, $path);

        foreach ($generator as $chunk) {
            yield from $this->writer->writeSiteMapIndex($path, $chunk, $this->dateFormat);
            yield new SiteMapIndex(dirname($path), $this->url, basename($path));

            ++$index;
            $path = $this->name($this->path, $group, 'index' . $index);
            $generator->file($path);
        }
    }
}
