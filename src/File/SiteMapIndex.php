<?php

namespace Robier\Sitemaps\File;

class SiteMapIndex implements Contract
{
    use Fileable;

    public function changeName(string $name): Contract
    {
        $this->name = $name;

        return $this;
    }
}
