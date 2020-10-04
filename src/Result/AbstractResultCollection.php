<?php declare(strict_types=1);

namespace GSoares\GoogleTrends\Result;

/**
 * @author Gabriel Felipe Soares <gabrielfs7@gmail.com>
 */
abstract class AbstractResultCollection implements ResultCollectionInterface
{
    /**
     * @var string
     */
    private $searchUrl;

    /**
     * @var array[]
     */
    private $results;

    /**
     * @var int
     */
    private $totalResults;

    public function __construct(string $searchUrl, array $results)
    {
        $this->searchUrl = $searchUrl;
        $this->results = $results;
        $this->totalResults = count($results);
    }

    public function getSearchUrl(): string
    {
        return $this->searchUrl;
    }

    public function getTotalResults(): int
    {
        return $this->totalResults;
    }

    public function getResults(): array
    {
        return $this->results;
    }

    public function jsonSerialize(): array
    {
        return [
            'searchUrl' => $this->getSearchUrl(),
            'totalResults' => $this->getTotalResults(),
            'results' => $this->getResults(),
        ];
    }
}
