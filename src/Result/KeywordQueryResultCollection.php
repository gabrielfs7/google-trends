<?php declare(strict_types=1);

namespace GSoares\GoogleTrends\Result;

use JsonSerializable;

/**
 * @author Gabriel Felipe Soares <gabrielfs7@gmail.com>
 */
class KeywordQueryResultCollection implements JsonSerializable
{
    /**
     * @var string
     */
    public $searchUrl;

    /**
     * @var KeywordQueryResult[]
     */
    public $results;

    /**
     * @var int
     */
    public $totalResults;

    public function __construct(string $searchUrl, KeywordQueryResult ...$terms)
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
     * @return KeywordQueryResult[]
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
            'searchUrl' => $this->searchUrl,
            'totalResults' => $this->totalResults,
            'results' => $this->results,
        ];
    }
}
