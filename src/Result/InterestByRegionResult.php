<?php declare(strict_types=1);

namespace GSoares\GoogleTrends\Result;

use JsonSerializable;

/**
 * @author Gabriel Felipe Soares <gabrielfs7@gmail.com>
 */
class InterestByRegionResult implements JsonSerializable
{
    /**
     * @var string
     */
    private $geoCode;

    /**
     * @var string
     */
    private $geoName;

    /**
     * @var int
     */
    private $value;

    /**
     * @var int
     */
    private $maxValueIndex;

    /**
     * @var bool
     */
    private $hasData;

    public function __construct(
        string $geoCode,
        string $geoName,
        int $value,
        int $maxValueIndex,
        bool $hasData
    ) {
        $this->geoCode = $geoCode;
        $this->geoName = $geoName;
        $this->value = $value;
        $this->maxValueIndex = $maxValueIndex;
        $this->hasData = $hasData;
    }

    public function getGeoCode(): string
    {
        return $this->geoCode;
    }

    public function getGeoName(): string
    {
        return $this->geoName;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function getMaxValueIndex(): int
    {
        return $this->maxValueIndex;
    }

    public function hasData(): bool
    {
        return $this->hasData;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize(): array
    {
        return [
            'geoCode' => $this->getGeoCode(),
            'geoName' => $this->getGeoName(),
            'value' => $this->getValue(),
            'maxValueIndex' => $this->getMaxValueIndex(),
            'hasData' => $this->hasData(),
        ];
    }
}
