<?php declare(strict_types=1);

namespace GSoares\GoogleTrends\Search;

use GSoares\GoogleTrends\Builder\ExploreUrlBuilder;
use GSoares\GoogleTrends\Error\GoogleTrendsException;
use GSoares\GoogleTrends\Result\ExploreResult;
use GSoares\GoogleTrends\Result\ExploreResultCollection;

/**
 * @author Gabriel Felipe Soares <gabrielfs7@gmail.com>
 */
class ExploreSearch
{
    /**
     * @var SearchRequest
     */
    private $searchRequest;

    public function __construct(SearchRequest $searchRequest = null)
    {
        $this->searchRequest = $searchRequest ?: new SearchRequest();
    }

    /**
     * @param ExploreUrlBuilder $exploreUrlBuilder
     *
     * @return ExploreResultCollection
     *
     * @throws GoogleTrendsException
     */
    public function search(ExploreUrlBuilder $exploreUrlBuilder): ExploreResultCollection
    {
        $response = $this->searchRequest->search($exploreUrlBuilder->build());

        $results = [];

        foreach ($response['widgets'] as $widget) {
            if (!isset($widget['token'], $widget['id'])) {
                throw new GoogleTrendsException(
                    sprintf(
                        'Missing request data for explore search. Got %s',
                        implode(', ', array_keys($widget))
                    )
                );
            }

            $results[] = new ExploreResult(
                $widget['id'],
                $widget['token']
            );
        }

        return new ExploreResultCollection(...$results);
    }
}
