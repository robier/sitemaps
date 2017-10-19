<?php

namespace Robier\Sitemaps\Driver\Writer;

use Robier\Sitemaps\File\SiteMap;
use Robier\Sitemaps\Location;
use XMLWriter;

class XML
{
    protected const EXTENSION = 'xml';
    protected const SCHEMA = 'http://www.sitemaps.org/schemas/sitemap/0.9';

    protected function new(string $path): XMLWriter
    {
        $xml = new XMLWriter();
        $xml->openURI($path);

        $xml->startDocument('1.0', 'UTF-8');
        $xml->setIndent(true);

        return $xml;
    }

    protected function close(XMLWriter $xml): void
    {
        while ($xml->endElement());
        $xml->endDocument();
    }

    public function writeSiteMap(string $path, \Iterator $locations, string $dateFormat): int
    {
        $xml = $this->new($path);

        $xml->startElement('urlset');
        $xml->writeAttribute('xmlns', static::SCHEMA);

        $count = 0;
        /** @var Location $location */
        foreach ($locations as $location) {
            $xml->startElement('url');
            $xml->writeElement('loc', $location->url());

            if ($location->priority()) {
                $xml->writeElement('priority', $location->priority());
            }

            if ($location->changeFrequency()) {
                $xml->writeElement('changefreq', $location->changeFrequency());
            }

            if ($location->lastModified()) {
                $xml->writeElement('lastmod', $location->lastModified()->format($dateFormat));
            }

            $xml->endElement();
            ++$count;
        }

        $this->close($xml);

        return $count;
    }

    public function writeSiteMapIndex(string $path, \Iterator $siteMaps, string $dateFormat): \Generator
    {
        $xml = $this->new($path);

        $xml->startElement('SiteMapIndexContract');
        $xml->writeAttribute('xmlns', static::SCHEMA);

        /** @var SiteMap $siteMap */
        $count = 0;
        foreach ($siteMaps as $siteMap) {
            $siteMap->changeSiteMapIndexFlag(true);
            yield $siteMap;

            $xml->startElement('url');
            $xml->writeElement('loc', $siteMap->fullUrl());

            if ($siteMap->lastModified()) {
                $xml->writeElement('lastmod', $siteMap->lastModified()->format($dateFormat));
            }

            $xml->endElement();
            ++$count;
        }

        $this->close($xml);

        return $count;
    }
}
