<?php declare(strict_types=1);

namespace GSoares\GoogleTrends\Dto;

use JsonSerializable;

/**
 * @author Gabriel Felipe Soares <gabrielfs7@gmail.com>
 */
class TermDto implements JsonSerializable
{
    /**
     * @var string
     */
    private $term;

    /**
     * @var bool
     */
    private $hasData;

    /**
     * @var int
     */
    private $ranking;

    /**
     * @var string
     */
    private $searchUrl;

    public function __construct(string $term, bool $hasData, int $ranking, string $searchUrl)
    {
        $this->term = $term;
        $this->hasData = $hasData;
        $this->ranking = $ranking;
        $this->searchUrl = $searchUrl;
    }

    public function getTerm(): string
    {
        return $this->term;
    }

    public function isHasData(): bool
    {
        return $this->hasData;
    }

    public function getRanking(): int
    {
        return $this->ranking;
    }

    public function getSearchUrl(): string
    {
        return $this->searchUrl;
    }

    public function jsonSerialize(): array
    {
        return [
            'term' => $this->term,
            'hasData' => $this->hasData,
            'ranking' => $this->ranking,
            'searchUrl' => $this->searchUrl,
        ];
    }
}
