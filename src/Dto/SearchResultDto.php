<?php declare(strict_types=1);

namespace GSoares\GoogleTrends\Dto;

use JsonSerializable;

/**
 * @author Gabriel Felipe Soares <gabrielfs7@gmail.com>
 */
class SearchResultDto implements JsonSerializable
{
    /**
     * @var string
     */
    public $searchUrl;

    /**
     * @var TermDto[]
     */
    public $results;

    public function __construct(string $searchUrl, TermDto ...$terms)
    {
        $this->searchUrl = $searchUrl;
        $this->results = $terms;
    }

    public function getSearchUrl(): string
    {
        return $this->searchUrl;
    }

    /**
     * @return TermDto[]
     */
    public function getResults(): array
    {
        return $this->results;
    }

    public function getTotalResults(): int
    {
        return count($this->results);
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize(): array
    {
        return [
            'searchUrl' => $this->searchUrl,
            'totalResults' => $this->getResults(),
            'results' => $this->getResults(),
        ];
    }
}
