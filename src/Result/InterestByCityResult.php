<?php declare(strict_types=1);

namespace GSoares\GoogleTrends\Result;

use JsonSerializable;

/**
 * @author Gabriel Felipe Soares <gabrielfs7@gmail.com>
 */
class InterestByCityResult implements JsonSerializable
{
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
        string $geoName,
        int $value,
        int $maxValueIndex,
        bool $hasData,
        string $lat,
        string $lng
    ) {
        $this->geoName = $geoName;
        $this->value = $value;
        $this->maxValueIndex = $maxValueIndex;
        $this->hasData = $hasData;
        $this->lat = $lat;
        $this->lng = $lng;
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
    
    public function getLat(): string
    {
        return $this->lat;
    }
    
    public function getLng(): string
    {
        return $this->lng;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize(): array
    {
        return [
            'geoName' => $this->getGeoName(),
            'value' => $this->getValue(),
            'maxValueIndex' => $this->getMaxValueIndex(),
            'hasData' => $this->hasData(),
            'lat' => $this->getLat(),
            'lng' => $this->getLng()
        ];
    }
}
