<?php declare(strict_types=1);

namespace GSoares\GoogleTrends\Result;

use JsonSerializable;

/**
 * @author Gabriel Felipe Soares <gabrielfs7@gmail.com>
 */
class InterestByRegionCollection implements JsonSerializable
{
    /**
     * @var string
     */
    private $searchUrl;

    /**
     * @var InterestByRegionResult[]
     */
    private $results;

    /**
     * @var int
     */
    private $totalResults;

    public function __construct(string $searchUrl, InterestByRegionResult ...$terms)
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
     * @return InterestByRegionResult[]
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
