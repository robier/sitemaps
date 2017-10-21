<?php

namespace Robier\Sitemaps\Tests\Unit\Middleware;

use ArrayIterator;
use PHPUnit\Framework\TestCase;
use Robier\Sitemaps\Middleware\MinLocations;

class MinLocationsTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     * @param int $limit
     * @param int $dataCount
     * @param \Iterator $data
     */
    public function testIterator(int $limit, int $dataCount, \Iterator $data)
    {
        $iterator = new MinLocations($limit);

        if ($limit > $dataCount) {
            $this->assertCount(0, $iterator->apply($data));
        } else {
            $this->assertCount($dataCount, $iterator->apply($data));
        }
    }

    public function dataProvider()
    {
        yield [10, 4, new ArrayIterator([1, 'test', 'data', 'another one'])];
        yield [4, 4, new ArrayIterator([1, 'test', 'data', 'another one'])];
        yield [3, 4, new ArrayIterator([1, 'test', 'data', 'another one'])];
        yield [5, 4, new ArrayIterator([1, 'test', 'data', 'another one'])];
        yield [1, 4, new ArrayIterator([1, 'test', 'data', 'another one'])];
    }
}
