<?php

namespace Robier\Sitemaps\File;

interface Contract
{
    public function name(): string;

    public function path(): string;

    public function fullPath(): string;

    public function url(): string;

    public function fullUrl(): string;

    public function changeName(string $name): self;
}
