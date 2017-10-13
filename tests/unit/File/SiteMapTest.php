<?php

namespace Robier\Sitemaps\Tests\Unit\File;

use PHPUnit\Framework\TestCase;
use Robier\Sitemaps\File\SiteMap;

class SiteMapTest extends TestCase
{
    public function dataProviderForGetters(): \Generator
    {
        $dateTime = new \DateTime();

        yield ['test', '/tmp/test/', 'http://google.com/', 'test-0', null];
        yield ['test', '/tmp/test/', 'http://google.com/', 'test-0', $dateTime];
    }

    /**
     * @dataProvider dataProviderForGetters
     *
     * @param $group
     * @param $path
     * @param $url
     * @param $name
     * @param $lastModified
     */
    public function testGetters($group, $path, $url, $name, $lastModified): void
    {
        $unit = new SiteMap($group, $path, $url, $name, $lastModified);

        $this->assertEquals($group, $unit->group());
        $this->assertEquals($path, $unit->path());
        $this->assertEquals($url, $unit->url());
        $this->assertEquals($name, $unit->name());
        $this->assertEquals($lastModified, $unit->lastModified());

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
        $unit = new SiteMap('group', $path, $url, 'name', null);

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
        $unit = new SiteMap('group', '/tmp/sitemap/', 'http://example.com', 'name', null);

        // change name should return new instance of SiteMap object with only changed name
        /** @var SiteMap $siteMap */
        $siteMap = $unit->changeName($name);

        $this->assertNotSame($unit, $siteMap);

        $this->assertEquals($unit->group(), $siteMap->group());
        $this->assertEquals($unit->path(), $siteMap->path());
        $this->assertEquals($unit->url(), $siteMap->url());

        $this->assertNotEquals($unit->name(), $siteMap->name());
        $this->assertNotEquals($unit->fullUrl(), $siteMap->fullUrl());
        $this->assertNotEquals($unit->fullPath(), $siteMap->fullPath());
    }
}
