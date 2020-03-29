<?php declare(strict_types=1);

namespace GSoares\GoogleTrends\Result;

/**
 * @method InterestOverTimeResult[] getResults()
 *
 * @author Gabriel Felipe Soares <gabrielfs7@gmail.com>
 */
class InterestOverTimeCollection extends AbstractResultCollection
{
    public function __construct(string $searchUrl, InterestOverTimeResult ...$results)
    {
        parent::__construct($searchUrl, $results);
    }
}
