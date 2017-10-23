<?php

namespace Robier\Sitemaps;

use DateTimeInterface;

class Location
{
    protected const CHANGE_FREQUENCY = [
        'always',
        'hourly',
        'daily',
        'weekly',
        'monthly',
        'yearly',
        'never',
    ];

    protected $url;
    protected $priority;
    protected $changeFrequency;
    protected $lastModified;
    /**
     * @var string
     */
    protected $subGroup;

    /**
     * Item constructor.
     *
     * @param string                 $url
     * @param float|null             $priority
     * @param string|null            $changeFrequency
     * @param DateTimeInterface|null $lastModified
     * @param string|null            $subGroup
     */
    public function __construct(string $url, float $priority = null, string $changeFrequency = null, DateTimeInterface $lastModified = null, string $subGroup = null)
    {
        $this->validateUrl($url);
        $this->validatePriority($priority);
        $this->validateChangeFrequency($changeFrequency);

        $this->url = $url;
        $this->priority = $priority;
        $this->changeFrequency = $changeFrequency;
        $this->lastModified = $lastModified;
        $this->subGroup = $subGroup;
    }

    protected function validateUrl(string $url): void
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException('Invalid url parameter');
        }
    }

    protected function validatePriority(float $priority = null): void
    {
        if (null === $priority) {
            return;
        }

        if ($priority < 0 || $priority > 1) {
            throw new \InvalidArgumentException('Invalid priority parameter');
        }
    }

    protected function validateChangeFrequency(string $changeFrequency = null): void
    {
        if (null === $changeFrequency) {
            return;
        }

        if (!in_array($changeFrequency, static::CHANGE_FREQUENCY)) {
            throw new \InvalidArgumentException('Invalid change frequency parameter');
        }
    }

    /**
     * @return string
     */
    public function url(): string
    {
        return $this->url;
    }

    /**
     * @return null|string
     */
    public function priority(): ?string
    {
        return $this->priority;
    }

    /**
     * @return null|string
     */
    public function changeFrequency(): ?string
    {
        return $this->changeFrequency;
    }

    /**
     * @return null|DateTimeInterface
     */
    public function lastModified(): ?DateTimeInterface
    {
        return $this->lastModified;
    }

    /**
     * @return null|string
     */
    public function subGroup(): ?string
    {
        return $this->subGroup;
    }
}
