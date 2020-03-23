<?php declare(strict_types=1);

namespace GSoares\GoogleTrends\Search;

use GSoares\GoogleTrends\Builder\ExploreUrlBuilder;
use GSoares\GoogleTrends\Builder\RelatedSearchUrlBuilder;
use GSoares\GoogleTrends\Error\GoogleTrendsException;
use GSoares\GoogleTrends\Result\KeywordResult;
use GSoares\GoogleTrends\Result\KeywordResultCollection;

/**
 * @author Gabriel Felipe Soares <gabrielfs7@gmail.com>
 */
class RelatedSearch
{
    private const TRENDS_URL = 'https://trends.google.com';

    /**
     * @var ExploreSearch
     */
    private $exploreSearch;

    /**
     * @var SearchRequest
     */
    private $searchRequest;

    public function __construct(ExploreSearch $exploreSearch = null, SearchRequest $searchRequest = null)
    {
        $this->searchRequest = $searchRequest ?: new SearchRequest();
        $this->exploreSearch = $exploreSearch ?: new ExploreSearch($this->searchRequest);
    }

    /**
     * @param RelatedSearchUrlBuilder $searchUrlBuilder
     * @return KeywordResultCollection
     *
     * @throws GoogleTrendsException
     */
    public function search(RelatedSearchUrlBuilder $searchUrlBuilder): KeywordResultCollection
    {
        $resultCollection = $this->exploreSearch->search(new ExploreUrlBuilder($searchUrlBuilder));

        $exploreResult = $resultCollection->getRelatedQueriesResult();

        if ($exploreResult === null) {
            throw new GoogleTrendsException('No token available!');
        }

        $searchUrlBuilder->withToken($exploreResult->getToken());

        $searchUrl = $searchUrlBuilder->build();

        $responseDecoded = $this->searchRequest->search($searchUrlBuilder->build());

        if (!isset($responseDecoded['default']['rankedList'])) {
            throw new GoogleTrendsException(
                sprintf(
                    'Invalid google response body "%s"...',
                    substr(var_export($responseDecoded, true), 100)
                )
            );
        }

        $results = [];

        foreach ($responseDecoded['default']['rankedList'] as $row) {
            foreach ($row['rankedKeyword'] ?? [] as $rank) {
                if (!isset($rank['query'], $rank['value'], $rank['link'])) {
                    throw new GoogleTrendsException(
                        sprintf(
                            'Google ranked list does not contain all keys. Only has: %s',
                            implode(', ', array_keys($rank))
                        )
                    );
                }

                $dto = new KeywordResult(
                    (string)$rank['query'],
                    (bool)($rank['hasData'] ?? false),
                    (int)$rank['value'],
                    self::TRENDS_URL . (string)$rank['link']
                );

                $results[] = $dto;
            }
        }

        return new KeywordResultCollection($searchUrl, ...$results);
    }
}
