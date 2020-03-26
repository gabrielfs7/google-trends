<?php declare(strict_types=1);

namespace GSoares\GoogleTrends\Result;

use GSoares\GoogleTrends\Error\GoogleTrendsException;

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
     * @return RelatedResult[]
     */
    public function getResults(): array
    {
        return $this->results;
    }

    /**
     * @return ExploreResult
     *
     * @throws GoogleTrendsException
     */
    public function getRelatedQueriesResult(): ExploreResult
    {
        foreach ($this->results as $result) {
            if ($result->isRelatedQueriesSearch()) {
                return $result;
            }
        }

        throw new GoogleTrendsException('No explore result available for related queries!');
    }

    /**
     * @return ExploreResult
     *
     * @throws GoogleTrendsException
     */
    public function getRelatedTopicsResult(): ExploreResult
    {
        foreach ($this->results as $result) {
            if ($result->isRelatedTopicsSearch()) {
                return $result;
            }
        }

        throw new GoogleTrendsException('No explore result available for related topics!');
    }
}
