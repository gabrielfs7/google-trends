<?php declare(strict_types=1);

namespace GSoares\GoogleTrends\Result;

use JsonSerializable;

/**
 * @author Gabriel Felipe Soares <gabrielfs7@gmail.com>
 */
class RelatedResult implements JsonSerializable
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
    private $value;

    /**
     * @var string
     */
    private $searchUrl;

    /**
     * @var string
     */
    private $metricType;

    public function __construct(string $term, bool $hasData, int $value, string $searchUrl, string $metricType = null)
    {
        $this->term = $term;
        $this->hasData = $hasData;
        $this->value = $value;
        $this->searchUrl = $searchUrl;
        $this->metricType = $metricType ?? 'TOP';
    }

    public function getTerm(): string
    {
        return $this->term;
    }

    public function hasData(): bool
    {
        return $this->hasData;
    }

    /**
     * @return int
     *
     * @deprecated Use $this::getValue()
     */
    public function getRanking(): int
    {
        return $this->getValue();
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function getSearchUrl(): string
    {
        return $this->searchUrl;
    }

    public function getMetricType(): string
    {
        return $this->metricType;
    }

    public function jsonSerialize(): array
    {
        return [
            'term' => $this->getTerm(),
            'hasData' => $this->hasData(),
            'ranking' => $this->getValue(),
            'value' => $this->getValue(),
            'searchUrl' => $this->getSearchUrl(),
            'metricType' => $this->getMetricType(),
        ];
    }
}
