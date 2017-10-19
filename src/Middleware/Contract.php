<?php

namespace Robier\Sitemaps\Middleware;

/**
 * Interface Contract
 */
interface Contract
{
    /**
     * @param \Iterator $items
     *
     * @return \Iterator
     */
    public function apply(\Iterator $items): \Iterator;
}
