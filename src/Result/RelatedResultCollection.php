<?php declare(strict_types=1);

namespace GSoares\GoogleTrends\Result;

/**
 * @method RelatedResult[] getResults()
 *
 * @author Gabriel Felipe Soares <gabrielfs7@gmail.com>
 */
class RelatedResultCollection extends AbstractResultCollection
{
    public function __construct(string $searchUrl, RelatedResult ...$results)
    {
        parent::__construct($searchUrl, $results);
    }
}
