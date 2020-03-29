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
        return $this->getResultByMethod(
            'isRelatedQueriesSearch',
            'No explore result available for related queries!'
        );
    }

    /**
     * @return ExploreResult
     *
     * @throws GoogleTrendsException
     */
    public function getRelatedTopicsResult(): ExploreResult
    {
        return $this->getResultByMethod(
            'isRelatedTopicsSearch',
            'No explore result available for related topics!'
        );
    }

    /**
     * @return ExploreResult
     *
     * @throws GoogleTrendsException
     */
    public function getInterestOverTimeResult(): ExploreResult
    {
        return $this->getResultByMethod(
            'isInterestOverTimeSearch',
            'No explore result available for interest over time!'
        );
    }

    /**
     * @return ExploreResult
     *
     * @throws GoogleTrendsException
     */
    public function getInterestByRegionResult(): ExploreResult
    {
        return $this->getResultByMethod(
            'isInterestByRegionSearch',
            'No explore result available for interest by region!'
        );
    }

    /**
     * @param string $method
     * @param string $exceptionMessage
     *
     * @return ExploreResult
     *
     * @throws GoogleTrendsException
     */
    private function getResultByMethod(string $method, string $exceptionMessage): ExploreResult
    {
        foreach ($this->results as $result) {
            if ($result->$method()) {
                return $result;
            }
        }

        throw new GoogleTrendsException($exceptionMessage);
    }
}
