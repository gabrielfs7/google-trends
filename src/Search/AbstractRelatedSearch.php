<?php declare(strict_types=1);

namespace GSoares\GoogleTrends\Search;

use GSoares\GoogleTrends\Error\GoogleTrendsException;
use GSoares\GoogleTrends\Result\RelatedResult;
use GSoares\GoogleTrends\Result\ExploreResultCollection;
use GSoares\GoogleTrends\Result\RelatedResultCollection;
use GSoares\GoogleTrends\Result\ResultCollectionInterface;

/**
 * @author Gabriel Felipe Soares <gabrielfs7@gmail.com>
 */
abstract class AbstractRelatedSearch implements SearchInterface
{
    protected const TRENDS_URL = 'https://trends.google.com';
    protected const RELATED_SEARCH_URL = 'https://trends.google.com/trends/api/widgetdata/relatedsearches';

    /**
     * @var ExploreSearch
     */
    protected $exploreSearch;

    /**
     * @var SearchRequest
     */
    protected $searchRequest;

    public function __construct(ExploreSearch $exploreSearch = null, SearchRequest $searchRequest = null)
    {
        $this->searchRequest = $searchRequest ?: new SearchRequest();
        $this->exploreSearch = $exploreSearch ?: new ExploreSearch($this->searchRequest);
    }

    /**
     * @param SearchFilter $searchFilter
     *
     * @return RelatedResultCollection
     *
     * @throws GoogleTrendsException
     */
    public function search(SearchFilter $searchFilter): ResultCollectionInterface
    {
        if (!$searchFilter->isConsideringRisingMetrics() && !$searchFilter->isConsideringTopMetrics()) {
            return new RelatedResultCollection('', ...[]);
        }

        $searchFilter->withToken($this->getToken($this->exploreSearch->search($searchFilter)));

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
                if (!$searchFilter->isConsideringRisingMetrics() && $this->isRisingMetric($rank)) {
                    continue;
                }

                if (!$searchFilter->isConsideringTopMetrics() && !$this->isRisingMetric($rank)) {
                    continue;
                }

                $results[] = $this->createResult($rank);
            }
        }

        return new RelatedResultCollection($searchUrl, ...$results);
    }

    /**
     * @param array $data
     *
     * @return RelatedResult
     *
     * @throws GoogleTrendsException
     */
    abstract protected function createResult(array $data): RelatedResult;

    /**
     * @param ExploreResultCollection $resultCollection
     *
     * @return string
     *
     * @throws GoogleTrendsException
     */
    abstract protected function getToken(ExploreResultCollection $resultCollection): string;

    abstract protected function getKeywordType(): string;

    protected function isRisingMetric(array $row): bool
    {
        return strpos(($row['formattedValue'] ?? ''), '+') === 0;
    }

    private function buildQuery(SearchFilter $searchFilter): string
    {
        $request = [
            'restriction' => [
                'geo' => [
                    'country' => $searchFilter->getLocation(),
                ],
                'time' => $searchFilter->getTime(),
                'originalTimeRangeForExploreUrl' => $searchFilter->getTime(),
            ],
            'keywordType' => $this->getKeywordType(),
            'metric' => [
                'TOP',
                'RISING',
            ],
            'trendinessSettings' => [
                'compareTime' => $searchFilter->getCompareTime(),
            ],
            'requestOptions' => [
                'property' => $searchFilter->getSearchType(),
                'backend' => 'IZG',
                'category' => $searchFilter->getCategory(),
            ],
            'language' => 'en',
            'userCountryCode' => $searchFilter->getLocation(),
        ];

        if (!empty($searchFilter->getSearchTerm())) {
            $request['restriction']['complexKeywordsRestriction'] = [
                'keyword' => [
                    [
                        'type' => 'BROAD',
                        'value' => $searchFilter->getSearchTerm(),
                    ],
                ],
            ];
        }

        $query = [
            'hl' => $searchFilter->getLanguage(),
            'tz' => '-120',
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
