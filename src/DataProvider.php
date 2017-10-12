<?php

namespace Robier\Sitemaps;

interface DataProvider
{
    /**
     * @return \Iterator
     */
    public function fetch(): \Iterator;
}
