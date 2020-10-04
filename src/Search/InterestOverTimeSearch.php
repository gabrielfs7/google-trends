<?php declare(strict_types=1);

namespace GSoares\GoogleTrends\Search;

use DateTimeImmutable;
use GSoares\GoogleTrends\Error\GoogleTrendsException;
use GSoares\GoogleTrends\Result\InterestOverTimeCollection;
use GSoares\GoogleTrends\Result\InterestOverTimeResult;
use GSoares\GoogleTrends\Result\ResultCollectionInterface;

/**
 * @author Gabriel Felipe Soares <gabrielfs7@gmail.com>
 */
class InterestOverTimeSearch implements SearchInterface
{
    private const SEARCH_URL = 'https://trends.google.com/trends/api/widgetdata/multiline';

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
     * @return InterestOverTimeCollection
     *
     * @throws GoogleTrendsException
     */
    public function search(SearchFilter $searchFilter): ResultCollectionInterface
    {
        $token = $this->exploreSearch
            ->search($searchFilter)
            ->getInterestOverTimeResult()
            ->getToken();

        $searchUrl = $this->buildQuery($searchFilter->withToken($token));

        $responseDecoded = $this->searchRequest->search($searchUrl);

        if (!isset($responseDecoded['default']['timelineData'])) {
            throw new GoogleTrendsException(
                sprintf(
                    'Invalid google response body "%s"...',
                    substr(var_export($responseDecoded, true), 100)
                )
            );
        }

        $results = [];

        foreach ($responseDecoded['default']['timelineData'] ?? [] as $row) {
            if (!isset($row['time'], $row['value'])) {
                throw new GoogleTrendsException(
                    sprintf(
                        'Google timeline list does not contain all keys. Only has: %s',
                        implode(', ', array_keys($row))
                    )
                );
            }

            $results[] = new InterestOverTimeResult(
                (new DateTimeImmutable(date('Y-m-d H:i:s', (int)$row['time']))),
                $row['value'],
                (bool)($row['hasData'] ?? false)
            );
        }

        return new InterestOverTimeCollection($searchUrl, ...$results);
    }

    private function buildQuery(SearchFilter $searchFilter): string
    {
        $request = [
            'time' => $searchFilter->getTime(),
            'resolution' => 'DAY',
            'locale' => $searchFilter->getLanguage(),
            'comparisonItem' => [
                [
                    'geo' => [
                        'country' => $searchFilter->getLocation(),
                    ],
                    'complexKeywordsRestriction' => [
                        'keyword' => [
                            [
                                'type' => 'BROAD',
                                'value' => $searchFilter->getSearchTerm(),
                            ],
                        ],
                    ]
                ],
            ],
            'requestOptions' => [
                'property' => '',
                'backend' => 'IZG',
                'category' => $searchFilter->getCategory(),
            ]
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

        return self::SEARCH_URL . '?' . $queryString;
    }
}
