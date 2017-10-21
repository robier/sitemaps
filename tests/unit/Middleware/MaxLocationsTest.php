<?php

namespace Robier\Sitemaps\Tests\Unit\Middleware;

use ArrayIterator;
use PHPUnit\Framework\TestCase;
use Robier\Sitemaps\Middleware\MaxLocations;

class MaxLocationsTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     *
     * @param int       $max
     * @param int       $dataCount
     * @param \Iterator $data
     */
    public function testIterator(int $max, int $dataCount, \Iterator $data)
    {
        $iterator = new MaxLocations($max);

        if ($max < $dataCount) {
            $this->assertCount($max, $iterator->apply($data));
        } else {
            $this->assertCount($dataCount, $iterator->apply($data));
        }
    }

    public function dataProvider()
    {
        yield [2, 4, new ArrayIterator([1, 'test', 'data', 'another one'])];
        yield [4, 4, new ArrayIterator([1, 'test', 'data', 'another one'])];
        yield [3, 4, new ArrayIterator([1, 'test', 'data', 'another one'])];
        yield [5, 4, new ArrayIterator([1, 'test', 'data', 'another one'])];
        yield [1, 4, new ArrayIterator([1, 'test', 'data', 'another one'])];
    }
}
