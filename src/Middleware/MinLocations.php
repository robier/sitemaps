<?php

namespace Robier\Sitemaps\Middleware;

/**
 * Class MinLocations
 *
 * If minimal locations are not meet, current sitemap will be skipped.
 */
class MinLocations implements Contract
{
    protected $quantity;

    /**
     * @param int $quantity
     */
    public function __construct(int $quantity)
    {
        $this->quantity = $quantity;
    }

    /**
     * @param \Iterator $items
     *
     * @return \Iterator
     */
    public function apply(\Iterator $items): \Iterator
    {
        $count = 1;
        $savedItems = [];

        foreach ($items as $item) {
            if ($count < $this->quantity) {
                $savedItems[] = $item;
                ++$count;
                continue;
            }

            if (!empty($savedItems)) {
                yield from $savedItems;

                $savedItems = []; // free memory
            }

            yield $item;
        }
    }
}
