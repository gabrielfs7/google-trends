<?php declare(strict_types=1);

namespace GSoares\GoogleTrends\Result;

use JsonSerializable;

/**
 * @author Gabriel Felipe Soares <gabrielfs7@gmail.com>
 */
interface ResultCollectionInterface extends JsonSerializable
{
    public function getSearchUrl(): string;

    public function getTotalResults(): int;

    public function getResults(): array;
}
