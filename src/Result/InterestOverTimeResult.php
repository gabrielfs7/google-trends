<?php declare(strict_types=1);

namespace GSoares\GoogleTrends\Result;

use DateTimeInterface;
use JsonSerializable;

/**
 * @author Gabriel Felipe Soares <gabrielfs7@gmail.com>
 */
class InterestOverTimeResult implements JsonSerializable
{
    /**
     * @var DateTimeInterface
     */
    private $interestAt;

    /**
     * @var int[]
     */
    private $values;

    /**
     * @var bool
     */
    private $hasData;

    public function __construct(DateTimeInterface $at, array $values, bool $hasData)
    {
        $this->interestAt = $at;
        $this->values = $values;
        $this->hasData = $hasData;
    }

    public function getInterestAt(): DateTimeInterface
    {
        return $this->interestAt;
    }

    public function getValues(): array
    {
        return $this->values;
    }

    public function getFirstValue(): int
    {
        return $this->values[0] ?? 0;
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
            'interestAt' => $this->getInterestAt()->format(DATE_ATOM),
            'values' => $this->getValues(),
            'firstValue' => $this->getFirstValue(),
            'hasData' => $this->hasData()
        ];
    }
}
