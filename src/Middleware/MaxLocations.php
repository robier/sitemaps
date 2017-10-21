<?php

namespace Robier\Sitemaps\Middleware;

/**
 * Class MaxLocations
 *
 * Number of links that will end up in one site map.
 */
class MaxLocations implements Contract
{
    protected $max;

    /**
     * @param int $max
     */
    public function __construct(int $max)
    {
        $this->max = $max;
    }

    /**
     * @param \Iterator $items
     *
     * @return \Iterator
     */
    public function apply(\Iterator $items): \Iterator
    {
        $count = 0;
        foreach ($items as $item) {
            yield $item;

            ++$count;

            if ($count >= $this->max) {
                break;
            }
        }
    }
}
