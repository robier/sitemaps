<?php

namespace Robier\Sitemaps\Iterators;

use ArrayIterator;
use Iterator;

class Combined implements Iterator
{
    /**
     * @var \Iterator
     */
    protected $data;
    protected $currentItem = 0;

    public function __construct(iterable ...$data)
    {
        foreach ($data as $item) {
            if ($item instanceof \Iterator) {
                $this->data[] = $item;
            } elseif (is_array($item)) {
                $this->data[] = new ArrayIterator($item);
            }
        }
    }

    /**
     * Return the current element
     *
     * @see http://php.net/manual/en/iterator.current.php
     *
     * @return mixed can return any type
     *
     * @since 5.0.0
     */
    public function current()
    {
        return $this->data[$this->currentItem]->current();
    }

    /**
     * Move forward to next element
     *
     * @see http://php.net/manual/en/iterator.next.php
     *
     * @return void any returned value is ignored
     *
     * @since 5.0.0
     */
    public function next(): void
    {
        $this->data[$this->currentItem]->next();
    }

    /**
     * Return the key of the current element
     *
     * @see http://php.net/manual/en/iterator.key.php
     *
     * @return mixed scalar on success, or null on failure
     *
     * @since 5.0.0
     */
    public function key()
    {
        return $this->data[$this->currentItem]->key();
    }

    /**
     * Checks if current position is valid
     *
     * @see http://php.net/manual/en/iterator.valid.php
     *
     * @return bool The return value will be casted to boolean and then evaluated.
     *              Returns true on success or false on failure.
     *
     * @since 5.0.0
     */
    public function valid(): bool
    {
        if (!$this->data[$this->currentItem]->valid()) {
            ++$this->currentItem;
        }

        if (isset($this->data[$this->currentItem])) {
            return $this->data[$this->currentItem]->valid();
        }

        return false;
    }

    /**
     * Rewind the Iterator to the first element
     *
     * @see http://php.net/manual/en/iterator.rewind.php
     *
     * @return void any returned value is ignored
     *
     * @since 5.0.0
     */
    public function rewind()
    {
        // no rewinding
    }
}
