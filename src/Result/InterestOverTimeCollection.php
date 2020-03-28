<?php declare(strict_types=1);

namespace GSoares\GoogleTrends\Result;

use JsonSerializable;

/**
 * @author Gabriel Felipe Soares <gabrielfs7@gmail.com>
 */
class InterestOverTimeCollection implements JsonSerializable
{
    /**
     * @var string
     */
    private $searchUrl;

    /**
     * @var InterestOverTimeResult[]
     */
    private $results;

    /**
     * @var int
     */
    private $totalResults;

    public function __construct(string $searchUrl, InterestOverTimeResult ...$terms)
    {
        $this->searchUrl = $searchUrl;
        $this->results = $terms;
        $this->totalResults = count($terms);
    }

    public function getSearchUrl(): string
    {
        return $this->searchUrl;
    }

    /**
     * @return InterestOverTimeResult[]
     */
    public function getResults(): array
    {
        return $this->results;
    }

    public function getTotalResults(): int
    {
        return $this->totalResults;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize(): array
    {
        return [
            'searchUrl' => $this->getSearchUrl(),
            'totalResults' => $this->getTotalResults(),
            'results' => $this->getResults(),
        ];
    }
}
