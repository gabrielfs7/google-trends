<?php declare(strict_types=1);

namespace GSoares\GoogleTrends\Search;

use GSoares\GoogleTrends\Error\GoogleTrendsException;
use GSoares\GoogleTrends\Result\KeywordQueryResult;
use GSoares\GoogleTrends\Result\KeywordQueryResultCollection;

/**
 * @author Gabriel Felipe Soares <gabrielfs7@gmail.com>
 */
class RelatedQuerySearch
{
    private const TRENDS_URL = 'https://trends.google.com';
    private const RELATED_SEARCH_URL = 'https://trends.google.com/trends/api/widgetdata/relatedsearches';

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
     * @param SearchFilter $searchFilter
     *
     * @return KeywordQueryResultCollection
     *
     * @throws GoogleTrendsException
     */
    public function search(SearchFilter $searchFilter): KeywordQueryResultCollection
    {
        $this->setUpToken($searchFilter);

        $searchUrl = $this->buildQuery($searchFilter);

        $responseDecoded = $this->searchRequest->search($searchUrl);

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

                $results[] = new KeywordQueryResult(
                    (string)$rank['query'],
                    (bool)($rank['hasData'] ?? false),
                    (int)$rank['value'],
                    self::TRENDS_URL . (string)$rank['link']
                );
            }
        }

        return new KeywordQueryResultCollection($searchUrl, ...$results);
    }

    /**
     * @param SearchFilter $searchFilter
     *
     * @return void
     *
     * @throws GoogleTrendsException
     */
    private function setUpToken(SearchFilter $searchFilter): void
    {
        $searchFilter->withToken(
            $this->exploreSearch->search($searchFilter)
                ->getRelatedQueriesResult()
                ->getToken()
        );
    }

    private function buildQuery(SearchFilter $searchFilter): string
    {
        $request = [
            'restriction' => [
                'geo' => [
                    'country' => $searchFilter->getLocation(),
                ],
                'time' => $searchFilter->getTime(),
                'originalTimeRangeForExploreUrl' => $searchFilter->getOriginalTimeRangeForExploreUrl(),
                'complexKeywordsRestriction' => [
                    'keyword' => [
                        [
                            'type' => 'BROAD',
                            'value' => $searchFilter->getSearchTerm(),
                        ],
                    ],
                ],
            ],
            'keywordType' => 'QUERY',
            'metric' => $searchFilter->getMetrics(),
            'trendinessSettings' => [
                'compareTime' => $searchFilter->getCompareTime(),
            ],
            'requestOptions' => [
                'property' => $searchFilter->getSearchType(),
                'backend' => 'IZG',
                'category' => $searchFilter->getCategory(),
            ],
            'language' => 'en',
        ];

        $query = [
            'hl' => $searchFilter->getLanguage(),
            'tz' => '-60',
            'req' => json_encode($request),
            'token' => $searchFilter->getToken()
        ];

        $queryString = str_replace(
            [
                '%3A',
                '%2C',
                '%2B'
            ],
            [
                ':',
                ',',
                '+',
            ],
            http_build_query($query)
        );

        return self::RELATED_SEARCH_URL . '?' . $queryString;
    }
}
