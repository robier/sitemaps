<?php

namespace Robier\Sitemaps\Tests\Functional;

use DateTimeInterface;
use PHPUnit\Framework\TestCase;
use Robier\Sitemaps\DataProvider;
use Robier\Sitemaps\Driver\Contract;
use Robier\Sitemaps\Generator;
use Robier\Sitemaps\Location;

abstract class BaseTest extends TestCase
{
    protected const TESTING_PATH = '/tmp/sitemaps-tests/';
    protected const TESTING_BASE_URL = 'http://example.com/';
    protected const EXTENSION = '';

    public static function setUpBeforeClass()
    {
        // create testing directory
        mkdir(static::TESTING_PATH, 0777, true);
    }

    public static function tearDownAfterClass()
    {
        // remove testing directory
        rmdir(static::TESTING_PATH);
    }

    protected function tearDown()
    {
        // remove testing files
        $files = array_diff(scandir(static::TESTING_PATH), ['.', '..']);
        foreach ($files as $file) {
            unlink(static::TESTING_PATH . $file);
        }
    }

    protected function assertSiteMapExists(string $name, int $index, string $subGroup = null, bool $gzipped = false): void
    {
        $this->assertFileExists($this->siteMapName($name, $index, $subGroup, $gzipped));
    }

    protected function assertSiteMapNotExists(string $name, int $index, string $subGroup = null, bool $gzipped = false): void
    {
        $this->assertFileNotExists($this->siteMapName($name, $index, $subGroup, $gzipped));
    }

    protected function assertSiteMapIndexExists(string $name, int $index, bool $gzipped = false): void
    {
        $this->assertFileExists($this->siteMapIndexName($name, $index, $gzipped));
    }

    protected function assertSiteMapIndexNotExists(string $name, int $index, bool $gzipped = false): void
    {
        $this->assertFileNotExists($this->siteMapIndexName($name, $index, $gzipped));
    }

    private function siteMapIndexName(string $name, int $index, bool $gzipped = false): string
    {
        $extension = static::EXTENSION;
        if ($gzipped) {
            $extension .= '.gz';
        }

        return sprintf('%s%s-index%d.%s', static::TESTING_PATH, $name, $index, $extension);
    }

    private function siteMapName(string $name, int $index, string $subGroup = null, bool $gzipped = false): string
    {
        $extension = static::EXTENSION;
        if ($gzipped) {
            $extension .= '.gz';
        }

        if (null === $subGroup) {
            return sprintf('%s%s-%d.%s', static::TESTING_PATH, $name, $index, $extension);
        }

        return sprintf('%s%s-%d-%s.%s', static::TESTING_PATH, $name, $index, $subGroup, $extension);
    }

    protected function makeGenerator(Contract $driver): Generator
    {
        return new Generator($driver);
    }

    protected function fakeDataProvider(int $count, string $subGroup = null, float $priority = null, string $changeFrequency = null, DateTimeInterface $lastModified = null): DataProvider
    {
        $url = static::TESTING_BASE_URL;

        return new class($count, $url, $subGroup, $priority, $changeFrequency, $lastModified) implements DataProvider {
            protected $count;
            protected $url;
            protected $subGroup;
            protected $priority;
            protected $changeFrequency;
            protected $lastModified;

            public function __construct(int $count, string $url, string $subGroup = null, float $priority = null, string $changeFrequency = null, DateTimeInterface $lastModified = null)
            {
                $this->count = $count;
                $this->url = $url;
                $this->subGroup = $subGroup;
                $this->priority = $priority;
                $this->changeFrequency = $changeFrequency;
                $this->lastModified = $lastModified;
            }

            /**
             * @return \Iterator
             */
            public function fetch(): \Iterator
            {
                for ($i = 1; $i <= $this->count; ++$i) {
                    yield new Location($this->url, $this->priority, $this->changeFrequency, $this->lastModified, $this->subGroup);
                }
            }
        };
    }

    protected function multipleFakeDataProviderGroups(int $count, array $groups, float $priority = null, string $changeFrequency = null, DateTimeInterface $lastModified = null): DataProvider
    {
        $providers = [];

        foreach ($groups as $group) {
            $providers[] = $this->fakeDataProvider($count, $group, $priority, $changeFrequency, $lastModified);
        }

        return new class(...$providers) implements DataProvider {
            protected $providers;

            public function __construct(DataProvider ...$providers)
            {
                $this->providers = $providers;
            }

            /**
             * @return \Iterator
             */
            public function fetch(): \Iterator
            {
                foreach ($this->providers as $provider) {
                    yield from $provider->fetch();
                }
            }
        };
    }

    protected function debug(): void
    {
        var_dump(array_diff(scandir(static::TESTING_PATH), ['.', '..']));
    }
}
