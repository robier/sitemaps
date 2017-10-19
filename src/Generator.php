<?php

namespace Robier\Sitemaps;

use Robier\Sitemaps\Driver\Base as DriverContract;
use Robier\Sitemaps\File\SiteMap;
use Robier\Sitemaps\File\SiteMapIndex;
use Robier\Sitemaps\Middleware\Contract as MiddlewareContract;
use Robier\Sitemaps\Processor\Contract as ProcessorContract;
use Traversable;

class Generator implements \IteratorAggregate
{
    /**
     * @var DriverContract
     */
    protected $writer;

    /**
     * @var ProcessorContract[]
     */
    protected $processors = [];

    /**
     * @var ProcessorContract[]
     */
    protected $postProcessors = [];

    /**
     * @var MiddlewareContract[]
     */
    protected $middleware = [];

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

    /**
     * @param string               $name
     * @param DataProvider         $dataProvider
     * @param MiddlewareContract[] ...$middleware
     *
     * @return Generator
     */
    public function data(string $name, DataProvider $dataProvider, MiddlewareContract ...$middleware): self
    {
        if (isset($this->data[$name])) {
            throw new \InvalidArgumentException('Duplicated data provider name ' . $name);
        }

        $this->data[$name] = $dataProvider;
        $this->middleware[$name] = $middleware;

        return $this;
    }

    /**
     * Processor gets all the files generated.
     * It's handy if you need to do anything with files, for example compress them.
     *
     * @param ProcessorContract   $processor
     * @param ProcessorContract[] ...$processors
     *
     * @return Generator
     */
    public function processor(ProcessorContract $processor, ProcessorContract ...$processors): self
    {
        // we are forcing 1st parameter
        array_unshift($processors, $processor);

        foreach ($processors as $processor) {
            $this->processors[] = $processor;
        }

        return $this;
    }

    /**
     * Post processor gets only "entry" files (site map index if exists, or sitemaps if site map index does not exists)
     * It's handy for pinging search engine providers or generating robots.txt file
     *
     * @param ProcessorContract   $processor
     * @param ProcessorContract[] ...$processors
     *
     * @return Generator
     */
    public function postProcessor(ProcessorContract $processor, ProcessorContract ...$processors): self
    {
        // we are forcing 1st parameter
        array_unshift($processors, $processor);

        foreach ($processors as $processor) {
            $this->postProcessors[] = $processor;
        }

        return $this;
    }

    public function generate(): \Iterator
    {
        if (empty($this->data)) {
            throw new \LogicException('Can not generate sitemaps as no data is provided');
        }

        /**
         * @var string
         * @var DataProvider $data
         */
        foreach ($this->data as $group => $data) {
            $generator = $data->fetch();

            /** @var MiddlewareContract $middleware */
            foreach ($this->middleware[$group] as $middleware) {
                $generator = $middleware->apply($generator);
            }

            $generator = $this->writer->write($group, $generator);

            /** @var ProcessorContract $processor */
            foreach ($this->processors as $processor) {
                $generator = $processor->apply($generator);
            }

            $generator = $this->onlyEntryPointFiles($generator);

            /** @var ProcessorContract $processor */
            foreach ($this->postProcessors as $processor) {
                $generator = $processor->apply($generator);
            }

            yield from $generator;
        }
    }

    /**
     * Yields only files that are considered as entry point to site maps. For example entry that you will add to
     * robots.txt or will ping search engines providers.
     *
     * @param \Iterator $generator
     * @return \Iterator
     */
    protected function onlyEntryPointFiles(\Iterator $generator): \Iterator
    {
        foreach ($generator as $item) {
            if ($item instanceof SiteMapIndex) {
                yield $item;
            }

            if ($item instanceof SiteMap && !$item->hasSiteMapIndex()) {
                yield $item;
            }
        }
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
