<?php

namespace Robier\Sitemaps\Driver;

interface Contract
{
    /**
     * Iterator must return instance of file contract
     *
     * @see \Robier\Sitemaps\File\Contract
     *
     * @param string    $group
     * @param \Iterator $items
     *
     * @return \Iterator|\Robier\Sitemaps\File\SiteMap[]|\Robier\Sitemaps\File\SiteMapIndex[]
     */
    public function write(string $group, \Iterator $items): \Iterator;
}
