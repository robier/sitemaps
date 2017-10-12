<?php

namespace Robier\Sitemaps\Processor;

class PassThought implements Contract
{
    protected $processor;

    public function __construct(callable $processor)
    {
        $this->processor = $processor;
    }

    public function apply(\Iterator $items): \Iterator
    {
        foreach ($items as $item) {
            // we do not want to be able to edit any item data
            call_user_func($this->processor, clone $item);
            yield $item;
        }
    }
}
