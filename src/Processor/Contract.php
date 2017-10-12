<?php

namespace Robier\Sitemaps\Processor;

/**
 * Interface Contract
 */
interface Contract
{
    public function apply(\Iterator $items): \Iterator;
}
