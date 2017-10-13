<?php

namespace Robier\Sitemaps\Tests\Unit\File;

use PHPUnit\Framework\TestCase;
use Robier\Sitemaps\File\SiteMapIndex;

class SiteMapIndexTest extends TestCase
{
    public function dataProviderForGetters(): \Generator
    {
        yield ['/tmp/test/', 'http://google.com/', 'test-0'];
    }

    /**
     * @dataProvider dataProviderForGetters
     *
     * @param $path
     * @param $url
     * @param $name
     */
    public function testGetters($path, $url, $name): void
    {
        $unit = new SiteMapIndex($path, $url, $name);

        $this->assertEquals($path, $unit->path());
        $this->assertEquals($url, $unit->url());
        $this->assertEquals($name, $unit->name());

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
        $unit = new SiteMapIndex($path, $url, 'name');

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
        $unit = new SiteMapIndex('/tmp/sitemap/', 'http://example.com', 'name');

        // change name should return new instance of SiteMap object with only changed name
        /** @var SiteMapIndex $siteMap */
        $siteMap = $unit->changeName($name);

        $this->assertNotSame($unit, $siteMap);

        $this->assertEquals($unit->path(), $siteMap->path());
        $this->assertEquals($unit->url(), $siteMap->url());

        $this->assertNotEquals($unit->name(), $siteMap->name());
        $this->assertNotEquals($unit->fullUrl(), $siteMap->fullUrl());
        $this->assertNotEquals($unit->fullPath(), $siteMap->fullPath());
    }
}
