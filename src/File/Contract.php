<?php

namespace Robier\Sitemaps\File;

use Countable;

interface Contract extends Countable
{
    public function name(): string;

    public function path(): string;

    public function fullPath(): string;

    public function url(): string;

    public function fullUrl(): string;

    public function hasSiteMapIndex(): bool;

    public function changeName(string $name): self;
}
