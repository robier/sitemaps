<?php

namespace Robier\Sitemaps\Tests\Unit\Processor;

use ArrayIterator;
use PHPUnit\Framework\TestCase;
use Robier\Sitemaps\Processor\PassThrough;

class PassThroughTest extends TestCase
{
    /**
     * @dataProvider plainDataProvider
     *
     * @param \Iterator $data
     * @param $value
     * @param $count
     */
    public function testPassingThroughScalar(\Iterator $data, $value, $count)
    {
        $counter = 0;
        $processor = new PassThrough(function ($item) use ($value, &$counter) {
            $this->assertEquals($value, $item);
            ++$counter;
        });

        foreach ($processor->apply($data) as $item);

        $this->assertEquals($count, $counter);
    }

    /**
     * @dataProvider objectDataProvider
     *
     * @param \Iterator $data
     * @param test      $value
     * @param $count
     */
    public function testPassingThroughObject(\Iterator $data, test $value, $count)
    {
        $counter = 0;
        $processor = new PassThrough(function ($item) use ($value, &$counter) {
            $this->assertInstanceOf(test::class, $item);
            $this->assertNotSame($value, $item);
            ++$counter;
        });

        foreach ($processor->apply($data) as $item);

        $this->assertEquals($count, $counter);
    }

    public function plainDataProvider(): \Generator
    {
        yield [new ArrayIterator(['test', 'test', 'test', 'test']), 'test', 4];
        yield [new ArrayIterator([1, 1, 1, 1, 1, 1, 1]), 1, 7];
        yield [new ArrayIterator([1.1, 1.1]), 1.1, 2];
    }

    public function objectDataProvider(): \Generator
    {
        $object = new test();

        yield [new ArrayIterator([$object, $object, $object, $object]), $object, 4];
    }
}

class test
{
}
