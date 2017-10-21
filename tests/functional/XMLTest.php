<?php

namespace Robier\Sitemaps\Tests\Functional;

use Robier\Sitemaps\Driver\XML;
use Robier\Sitemaps\File\SiteMap;
use Robier\Sitemaps\Processor\GZip;

class XMLTest extends BaseTest
{
    protected const EXTENSION = 'xml';

    public function simpleSiteMapDataProvider()
    {
        yield [500, 1, 1];
        yield [10000, 1, 1];
        yield [100000, 1, 2];
        yield [150000, 1, 3];
        yield [1000000, 1, 20];
    }

    /**
     * @dataProvider simpleSiteMapDataProvider
     *
     * @param int $linksCount
     * @param int $expectedSiteMapIndexFileCount
     * @param int $expectSiteMapFilesCount
     */
    public function testGenerateSimpleSiteMap(int $linksCount, int $expectedSiteMapIndexFileCount, int $expectSiteMapFilesCount): void
    {
        $generator = $this->makeGenerator(new XML(static::TESTING_PATH, static::TESTING_BASE_URL));

        $generator->data('sitemap', $this->fakeDataProvider($linksCount));

        foreach ($generator as $item);

        if ($expectSiteMapFilesCount > 1) {
            // if we have only one site map there is no site map index
            for ($i = 0; $i < $expectedSiteMapIndexFileCount; ++$i) {
                $this->assertSiteMapIndexExists('sitemap', $i);
                $this->assertSiteMapIndexNotExists('sitemap', $i, true);
            }
        } else {
            $this->assertSiteMapIndexNotExists('sitemap', 0);
            $this->assertSiteMapIndexNotExists('sitemap', 0, true);
        }

        for ($i = 0; $i < $expectSiteMapFilesCount; ++$i) {
            $this->assertSiteMapExists('sitemap', $i);
            $this->assertSiteMapNotExists('sitemap', $i, null, true);
        }
    }

    /**
     * @dataProvider simpleSiteMapDataProvider
     *
     * @param int $linksCount
     * @param int $expectedSiteMapIndexFileCount
     * @param int $expectSiteMapFilesCount
     */
    public function testGenerateSimpleSiteMapForcingSiteMapIndexes(int $linksCount, int $expectedSiteMapIndexFileCount, int $expectSiteMapFilesCount): void
    {
        $generator = $this->makeGenerator(new XML(static::TESTING_PATH, static::TESTING_BASE_URL, XML::DATE, true));

        $generator->data('sitemap', $this->fakeDataProvider($linksCount));

        foreach ($generator as $item);

        for ($i = 0; $i < $expectedSiteMapIndexFileCount; ++$i) {
            $this->assertSiteMapIndexExists('sitemap', $i);
            $this->assertSiteMapIndexNotExists('sitemap', $i, true);
        }

        for ($i = 0; $i < $expectSiteMapFilesCount; ++$i) {
            $this->assertSiteMapExists('sitemap', $i);
            $this->assertSiteMapNotExists('sitemap', $i, null, true);
        }
    }

    /**
     * @dataProvider simpleSiteMapDataProvider
     *
     * @param int $linksCount
     * @param int $expectedSiteMapIndexFileCount
     * @param int $expectSiteMapFilesCount
     */
    public function testGenerateGZipSiteMaps(int $linksCount, int $expectedSiteMapIndexFileCount, int $expectSiteMapFilesCount): void
    {
        $generator = $this->makeGenerator(new XML(static::TESTING_PATH, static::TESTING_BASE_URL, XML::DATE, true));

        $generator->data('sitemap', $this->fakeDataProvider($linksCount));

        $generator->processor(new GZip());

        foreach ($generator as $item);

        for ($i = 0; $i < $expectedSiteMapIndexFileCount; ++$i) {
            $this->assertSiteMapIndexExists('sitemap', $i, true);
            $this->assertSiteMapIndexNotExists('sitemap', $i);
        }

        for ($i = 0; $i < $expectSiteMapFilesCount; ++$i) {
            $this->assertSiteMapExists('sitemap', $i, null, true);
            $this->assertSiteMapNotExists('sitemap', $i);
        }
    }

    public function testGenerateGroupedSiteMapsWithoutForcingSiteMapIndexes(): void
    {
        $generator = $this->makeGenerator(new XML(static::TESTING_PATH, static::TESTING_BASE_URL));

        $generator->data('sitemap', $this->fakeDataProvider(500, 'test'));

        foreach ($generator as $item);

        $this->assertSiteMapExists('sitemap', 0, 'test');
        $this->assertSiteMapNotExists('sitemap', 0, 'test', true);
        $this->assertSiteMapIndexNotExists('sitemap', 0);
        $this->assertSiteMapIndexNotExists('sitemap', 0, true);
    }

    public function testGenerateGroupedSiteMapsForcingSiteMapIndexes(): void
    {
        $generator = $this->makeGenerator(new XML(static::TESTING_PATH, static::TESTING_BASE_URL, XML::DATE, true));

        $generator->data('sitemap', $this->fakeDataProvider(500, 'test'));

        foreach ($generator as $item);

        $this->assertSiteMapExists('sitemap', 0, 'test');
        $this->assertSiteMapNotExists('sitemap', 0, 'test', true);
        $this->assertSiteMapIndexExists('sitemap', 0);
        $this->assertSiteMapIndexNotExists('sitemap', 0, true);
    }

    public function testGenerateMultipleGroupedSiteMaps(): void
    {
        $generator = $this->makeGenerator(new XML(static::TESTING_PATH, static::TESTING_BASE_URL));

        $subGroups = ['test', 'example', null];

        $generator->data('sitemap', $this->multipleFakeDataProviderGroups(500, $subGroups));

        foreach ($generator as $item);

        foreach ($subGroups as $index => $subGroup) {
            $this->assertSiteMapExists('sitemap', $index, $subGroup);
            $this->assertSiteMapNotExists('sitemap', $index, $subGroup, true);
        }

        $this->assertSiteMapIndexExists('sitemap', 0);
        $this->assertSiteMapIndexNotExists('sitemap', 0, true);
    }
}
