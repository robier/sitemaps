<?php

namespace Robier\Sitemaps\Tests\Unit\File;

use PHPUnit\Framework\TestCase;
use Robier\Sitemaps\File\SiteMap;

class SiteMapTest extends TestCase
{
    public function dataProviderForGetters(): \Generator
    {
        $dateTime = new \DateTime();

        yield [10, 'test', '/tmp/test/', 'http://google.com/', 'test-0', null];
        yield [15, 'test', '/tmp/test/', 'http://google.com/', 'test-0', $dateTime];
    }

    /**
     * @dataProvider dataProviderForGetters
     *
     * @param $count
     * @param $group
     * @param $path
     * @param $url
     * @param $name
     * @param $lastModified
     */
    public function testGetters($count, $group, $path, $url, $name, $lastModified): void
    {
        $unit = new SiteMap($count, $group, $path, $url, $name, $lastModified);

        $this->assertCount($count, $unit);
        $this->assertEquals($count, $unit->count());
        $this->assertEquals($group, $unit->group());
        $this->assertEquals($path, $unit->path());
        $this->assertEquals($url, $unit->url());
        $this->assertEquals($name, $unit->name());
        $this->assertEquals($lastModified, $unit->lastModified());

        // special getters

        $this->assertEquals($path . $name, $unit->fullPath());
        $this->assertEquals($url . $name, $unit->fullUrl());
    }

    public function testSettingSiteMapIndexFlag(): void
    {
        $unit = new SiteMap(3, 'test', '/tmp/', 'http://example.com/', 'test');

        $this->assertFalse($unit->hasSiteMapIndex());

        $this->assertSame($unit, $unit->changeSiteMapIndexFlag(true));

        $this->assertTrue($unit->hasSiteMapIndex());

        $this->assertSame($unit, $unit->changeSiteMapIndexFlag(false));

        $this->assertFalse($unit->hasSiteMapIndex());
    }

    public function dataProviderForPathAndUrlFix(): \Generator
    {
        yield ['/tmp/test', '/tmp/test/', 'http://google.com', 'http://google.com/'];
        yield ['/tmp/test/', '/tmp/test/', 'http://google.com', 'http://google.com/'];
        yield ['/tmp/test', '/tmp/test/', 'http://google.com/', 'http://google.com/'];
        yield ['/tmp/test/', '/tmp/test/', 'http://google.com/', 'http://google.com/'];
    }

    /**
     * @dataProvider dataProviderForPathAndUrlFix
     *
     * @param $path
     * @param $expectedPath
     * @param $url
     * @param $expectedUrl
     */
    public function testPathAndUrlFix($path, $expectedPath, $url, $expectedUrl): void
    {
        $unit = new SiteMap(5, 'group', $path, $url, 'name', null);

        $this->assertEquals($expectedPath, $unit->path());
        $this->assertEquals($expectedUrl, $unit->url());
    }

    public function dataProviderForChangeNameMethod()
    {
        yield ['test'];
        yield ['example'];
        yield ['test123'];
    }

    /**
     * @dataProvider dataProviderForChangeNameMethod
     *
     * @param string $name
     */
    public function testChangeNameMethod(string $name): void
    {
        $unit = new SiteMap(5, 'group', '/tmp/sitemap/', 'http://example.com', 'name', null);

        /** @var SiteMap $siteMap */
        $siteMap = $unit->changeName($name);

        $this->assertSame($unit, $siteMap);
    }
}
