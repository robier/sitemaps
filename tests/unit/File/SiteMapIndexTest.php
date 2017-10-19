<?php

namespace Robier\Sitemaps\Tests\Unit\File;

use PHPUnit\Framework\TestCase;
use Robier\Sitemaps\File\SiteMapIndex;

class SiteMapIndexTest extends TestCase
{
    public function dataProviderForGetters(): \Generator
    {
        yield [5, '/tmp/test/', 'http://google.com/', 'test-0'];
    }

    /**
     * @dataProvider dataProviderForGetters
     *
     * @param $count
     * @param $path
     * @param $url
     * @param $name
     */
    public function testGetters($count, $path, $url, $name): void
    {
        $unit = new SiteMapIndex($count, $path, $url, $name);

        $this->assertCount($count, $unit);
        $this->assertEquals($count, $unit->count());
        $this->assertEquals($path, $unit->path());
        $this->assertEquals($url, $unit->url());
        $this->assertEquals($name, $unit->name());
        $this->assertFalse($unit->hasSiteMapIndex());

        // special getters

        $this->assertEquals($path . $name, $unit->fullPath());
        $this->assertEquals($url . $name, $unit->fullUrl());
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
        $unit = new SiteMapIndex(5, $path, $url, 'name');

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
        $unit = new SiteMapIndex(5, '/tmp/sitemap/', 'http://example.com', 'name');

        /** @var SiteMapIndex $siteMap */
        $siteMap = $unit->changeName($name);

        $this->assertSame($unit, $siteMap);
    }
}
