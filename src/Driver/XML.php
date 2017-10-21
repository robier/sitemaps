<?php

namespace Robier\Sitemaps\Driver;

use Robier\Sitemaps\Driver\Writer\XML as XMLWriter;
use Robier\Sitemaps\File\SiteMap as SiteMapFile;
use Robier\Sitemaps\File\SiteMapIndex;
use Robier\Sitemaps\Iterators\Combined;

class XML extends Base
{
    const EXTENSION = 'xml';

    const DATE = 'Y-m-d';
    const DATETIME = \DateTime::W3C;

    protected const INDEX_FILE_SUFFIX = 'index';

    protected $writer;
    protected $dateFormat;

    protected $forceSiteMapIndexFiles;

    public function __construct(string $path, string $url, string $dateFormat = self::DATE, bool $forceSiteMapIndexFiles = false)
    {
        parent::__construct($path, $url);

        $this->dateFormat = $dateFormat;
        $this->forceSiteMapIndexFiles = $forceSiteMapIndexFiles;

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

        $path = $this->name($this->path, $group, $index, $items);
        $generator = $this->chunk($items);

        foreach ($generator->file($path) as $chunk) {
            $linksCount = $this->writer->writeSiteMap($path, $chunk, $this->dateFormat);
            yield new SiteMapFile($linksCount, $group, dirname($path), $this->url, basename($path));

            ++$index;
            $path = $this->name($this->path, $group, $index, $items);
        }
    }

    protected function siteMapIndex(string $group, \Iterator $items): \Generator
    {
        $index = 0;

        $path = $this->indexName($this->path, $group, $index);
        $generator = $this->chunk($items);

        foreach ($generator->file($path) as $chunk) {
            $siteMapIndexNeeded = true; // in any case this variable is overwritten in checkIfSiteMapIndexNeeded method
            $siteMapItems = $this->checkIfSiteMapIndexNeeded($index, $chunk, $siteMapIndexNeeded);

            if ($siteMapIndexNeeded) {
                $siteMapIndexGenerator = $this->writer->writeSiteMapIndex($path, $siteMapItems, $this->dateFormat);
                yield from $siteMapIndexGenerator;
                yield new SiteMapIndex($siteMapIndexGenerator->getReturn(), dirname($path), $this->url, basename($path));
            } else {
                yield from $siteMapItems;
            }

            ++$index;
            $path = $this->indexName($this->path, $group, $index);
        }
    }

    protected function checkIfSiteMapIndexNeeded(int $index, \Iterator $items, bool &$siteMapIndexNeededFlag): \Iterator
    {
        if (0 !== $index || $this->forceSiteMapIndexFiles) {
            $siteMapIndexNeededFlag = true;

            return $items;
        }

        $cachedItems = [];
        $siteMapIndexNeededFlag = false;
        foreach ($items as $item) {
            $cachedItems[] = $item;

            if (count($cachedItems) > 1) {
                $siteMapIndexNeededFlag = true;
                break;
            }
        }

        return new Combined($cachedItems, $items);
    }

    protected function indexName(string $path, string $group, int $index): string
    {
        return $this->name($path, $group, static::INDEX_FILE_SUFFIX . $index);
    }
}
