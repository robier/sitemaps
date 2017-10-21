<?php

namespace Robier\Sitemaps\Iterators;

use Robier\Sitemaps\Location;

class FileChunk implements \Iterator
{
    protected $iterator;
    protected $maxItems;
    protected $maxBytes;

    protected $file;

    protected $currentSubGroup = null;

    public function __construct(\Iterator $iterator, int $maxItems, int $maxBytes)
    {
        $this->iterator = $iterator;
        $this->maxBytes = $maxBytes - 100; // remove 100 bytes so we do not spill over $maxBites
        $this->maxItems = $maxItems;

        $this->currentSubGroup = $iterator->valid() && $iterator->current() instanceof Location ? $iterator->current()->subGroup() : null;
    }

    public function file(string $path): self
    {
        $this->file = $path;

        return $this;
    }

    protected function validate(int $lineNumber): bool
    {
        if ($this->iterator->current() instanceof Location && $this->iterator->current()->subGroup() !== $this->currentSubGroup) {
            $this->currentSubGroup = $this->iterator->current()->subGroup();

            return false;
        }

        if ($lineNumber >= $this->maxItems) {
            return false;
        }

        // file not provided
        if (null === $this->file) {
            throw new \InvalidArgumentException('File property not defined!');
        }

        clearstatcache(true, $this->file);
        if (!is_readable($this->file)) {
            // file does not exists
            return true;
        }

        return filesize($this->file) < $this->maxBytes;
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
    public function current(): \Generator
    {
        $lineNumber = 0;
        while ($this->iterator->valid() && $this->validate($lineNumber)) {
            yield $this->iterator->current();
            $this->iterator->next();
            ++$lineNumber;
        }
    }

    /**
     * Move forward to next element
     *
     * @see http://php.net/manual/en/iterator.next.php
     *
     * @return void any returned value is ignored
     *
     * @see FileChunk::current()
     * @since 5.0.0
     */
    public function next(): void
    {
        // we are doing $iterator->next() in current() method
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
        return $this->iterator->key();
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
        return $this->iterator->valid();
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
    public function rewind(): void
    {
        $this->iterator->rewind();
    }
}
