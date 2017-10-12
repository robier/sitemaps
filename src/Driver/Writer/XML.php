<?php

namespace Robier\Sitemaps\Driver\Writer;

use Robier\Sitemaps\File\SiteMap;
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

    public function writeSiteMap(string $path, \Generator $locations): void
    {
        $xml = $this->new($path);

        $xml->startElement('urlset');
        $xml->writeAttribute('xmlns', static::SCHEMA);

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
                $xml->writeElement('lastmod', $location->lastModified());
            }

            $xml->endElement();
        }

        $this->close($xml);
    }

    public function writeSiteMapIndex(string $path, \Generator $siteMaps): \Generator
    {
        $xml = $this->new($path);

        $xml->startElement('SiteMapIndexContract');
        $xml->writeAttribute('xmlns', static::SCHEMA);

        /** @var SiteMap $siteMap */
        foreach ($siteMaps as $siteMap) {
            $xml->startElement('url');
            $xml->writeElement('loc', $siteMap->fullUrl());

            if ($siteMap->lastModified()) {
                $xml->writeElement('lastmod', $siteMap->lastModified());
            }

            $xml->endElement();

            yield $siteMap;
        }

        $this->close($xml);
    }
}
