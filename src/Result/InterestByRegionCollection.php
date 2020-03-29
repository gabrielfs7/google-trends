<?php declare(strict_types=1);

namespace GSoares\GoogleTrends\Result;

/**
 * @method InterestByRegionResult[] getResults()
 *
 * @author Gabriel Felipe Soares <gabrielfs7@gmail.com>
 */
class InterestByRegionCollection extends AbstractResultCollection
{
    public function __construct(string $searchUrl, InterestByRegionResult ...$results)
    {
        parent::__construct($searchUrl, $results);
    }
}
