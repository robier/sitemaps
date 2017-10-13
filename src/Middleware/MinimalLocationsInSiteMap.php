<?php

namespace Robier\Sitemaps\Middleware;

/**
 * Class MinimalLocationsInSiteMap
 *
 * If minimal locations are not meet, current sitemap will be skipped.
 *
 * @package Robier\Sitemaps\Processor\Location
 */
class MinimalLocationsInSiteMap implements Contract
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
     * @return \Iterator
     */
    public function apply(\Iterator $items): \Iterator
    {
        $count = 1;
        $savedItems = [];

        foreach ($items as $item){

            if($count < $this->quantity){
                $savedItems[] = $item;
                ++$count;
                continue;
            }

            if(!empty($savedItems)){
                foreach($savedItems as $savedItem){
                    // yield all collected items at once
                    yield $savedItem;
                }

                $savedItems = []; // free memory
            }

            yield $item;
        }
    }
}
