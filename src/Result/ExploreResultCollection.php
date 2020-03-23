<?php declare(strict_types=1);

namespace GSoares\GoogleTrends\Result;

/**
 * @author Gabriel Felipe Soares <gabrielfs7@gmail.com>
 */
class ExploreResultCollection
{
    /**
     * @var ExploreResult[]
     */
    public $results;

    public function __construct(ExploreResult ...$terms)
    {
        $this->results = $terms;
    }

    /**
     * @return KeywordResult[]
     */
    public function getResults(): array
    {
        return $this->results;
    }

    public function getRelatedQueriesResult(): ?ExploreResult
    {
        foreach ($this->results as $result) {
            if ($result->isRelatedQueriesSearch()) {
                return $result;
            }
        }

        return null;
    }
}
