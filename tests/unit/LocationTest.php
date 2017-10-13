<?php

namespace Robier\Sitemaps\Tests\Unit;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Robier\Sitemaps\Location;

class LocationTest extends TestCase
{
    public function dataProviderForGetters(): \Generator
    {
        $dateTime = new \DateTime();

        yield ['https://example.com/', 0.1,  'weekly', $dateTime];
        yield ['https://example.com/', 0.1,  'weekly',  null];
        yield ['https://example.com/', 0.1,  null,     $dateTime];
        yield ['https://example.com/', null, 'weekly', $dateTime];
        yield ['https://example.com/', null, null,     $dateTime];
        yield ['https://example.com/', 0.1,  null,     null];
        yield ['https://example.com/', null, 'weekly', null];
    }

    /**
     * @dataProvider dataProviderForGetters
     *
     * @param $url
     * @param $priority
     * @param $changeFrequency
     * @param $lastModified
     */
    public function testGetters($url, $priority, $changeFrequency, $lastModified): void
    {
        $unit = new Location($url, $priority, $changeFrequency, $lastModified);

        $this->assertEquals($url, $unit->url());
        $this->assertEquals($priority, $unit->priority());
        $this->assertEquals($changeFrequency, $unit->changeFrequency());
        $this->assertEquals($lastModified, $unit->lastModified());
    }

    public function dataProviderForValidationFail(): \Generator
    {
        yield ['/https://example.com/', null, null, null, 'Invalid url parameter'];
        yield ['://example.com/', null, null, null, 'Invalid url parameter'];
        yield ['example', null, null, null, 'Invalid url parameter'];
        yield ['https://example.com/', -2, null, null, 'Invalid priority parameter'];
        yield ['https://example.com/', 1.1, null, null, 'Invalid priority parameter'];
        yield ['https://example.com/', 1.0000001, null, null, 'Invalid priority parameter'];
        yield ['https://example.com/', -1, null, null, 'Invalid priority parameter'];
        yield ['https://example.com/', null, 'test', null, 'Invalid change frequency parameter'];
        yield ['https://example.com/', null, 'random', null, 'Invalid change frequency parameter'];
    }

    /**
     * @dataProvider dataProviderForValidationFail
     *
     * @param $url
     * @param $priority
     * @param $changeFrequency
     * @param $lastModified
     * @param $message
     */
    public function testValidationFail($url, $priority, $changeFrequency, $lastModified, $message): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($message);

        new Location($url, $priority, $changeFrequency, $lastModified);
    }

    public function dataProviderChangeFrequencies(): \Generator
    {
        yield ['always'];
        yield ['hourly'];
        yield ['daily'];
        yield ['weekly'];
        yield ['monthly'];
        yield ['yearly'];
        yield ['never'];
    }

    /**
     * @dataProvider dataProviderChangeFrequencies
     *
     * @param string $frequency
     */
    public function testChangeFrequency(string $frequency): void
    {
        $unit = new Location('http://example.com', 0.5, $frequency);

        $this->assertEquals($frequency, $unit->changeFrequency());
    }
}
