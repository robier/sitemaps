<?php

namespace Robier\Sitemaps\Processor;

class PassThrough implements Contract
{
    protected $processor;

    public function __construct(callable $processor)
    {
        $this->processor = $processor;
    }

    public function apply(\Iterator $items): \Iterator
    {
        foreach ($items as $item) {
            if (is_object($item)) {
                // we do not want to be able to edit any item data
                call_user_func($this->processor, clone $item);
            } else {
                call_user_func($this->processor, $item);
            }

            yield $item;
        }
    }
}
