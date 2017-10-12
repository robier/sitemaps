<?php

namespace Robier\Sitemaps;

use Robier\Sitemaps\Driver\Base as DriverContract;
use Robier\Sitemaps\Processor\Contract as ProcessorContract;
use Traversable;

class Generator implements \IteratorAggregate
{
    /**
     * @var DriverContract
     */
    protected $writer;

    /**
     * @var ProcessorContract
     */
    protected $processors = [];

    protected $data = [];

    /**
     * Generator constructor.
     *
     * @param DriverContract $writer
     */
    public function __construct(DriverContract $writer)
    {
        $this->writer = $writer;
    }

    public function data(string $name, DataProvider $dataProvider): self
    {
        if (isset($this->data[$name])) {
            throw new \InvalidArgumentException('Duplicated data provider name ' . $name);
        }

        $this->data[$name] = $dataProvider->fetch();

        return $this;
    }

    public function processor(ProcessorContract $processor): self
    {
        $this->processors[] = $processor;

        return $this;
    }

    public function generate(): \Iterator
    {
        if (empty($this->data)) {
            throw new \LogicException('Can not generate sitemaps as no data is provided');
        }

        foreach ($this->data as $group => $data) {
            $generator = $this->writer->write($group, $data);

            /** @var ProcessorContract $processor */
            foreach ($this->processors as $processor) {
                $generator = $processor->apply($generator);
            }
        }

        // @todo handle no data fetched
        return $generator;
    }

    /**
     * Retrieve an external iterator
     *
     * @see http://php.net/manual/en/iteratoraggregate.getiterator.php
     *
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     *                     <b>Traversable</b>
     *
     * @since 5.0.0
     */
    public function getIterator(): \Iterator
    {
        return $this->generate();
    }
}
