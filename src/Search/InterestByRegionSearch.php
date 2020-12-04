<?php declare(strict_types=1);

namespace GSoares\GoogleTrends\Search;

use GSoares\GoogleTrends\Error\GoogleTrendsException;
use GSoares\GoogleTrends\Result\InterestByRegionCollection;
use GSoares\GoogleTrends\Result\InterestByRegionResult;
use GSoares\GoogleTrends\Result\ResultCollectionInterface;
use GSoares\GoogleTrends\Result\InterestByCityCollection;
use GSoares\GoogleTrends\Result\InterestByCityResult;

/**
 * @author Gabriel Felipe Soares <gabrielfs7@gmail.com>
 */
class InterestByRegionSearch implements SearchInterface
{
    private const SEARCH_URL = 'https://trends.google.com/trends/api/widgetdata/comparedgeo';

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
     * @return InterestByRegionCollection
     *
     * @throws GoogleTrendsException
     */
    public function search(SearchFilter $searchFilter): ResultCollectionInterface
    {
        $token = $this->exploreSearch
            ->search($searchFilter)
            ->getInterestByRegionResult()
            ->getToken();

        $searchUrl = $this->buildQuery($searchFilter->withToken($token));
//        echo $searchUrl;
//        exit;
        $responseDecoded = $this->searchRequest->search($searchUrl);

        if (!isset($responseDecoded['default']['geoMapData'])) {
            throw new GoogleTrendsException(
                sprintf(
                    'Invalid google response body "%s"...',
                    substr(var_export($responseDecoded, true), 100)
                )
            );
        }

        $results = [];
//        echo "<pre>";
//        print_r($responseDecoded['default']['geoMapData']);
//        exit;
        foreach ($responseDecoded['default']['geoMapData'] ?? [] as $row) {
            if($searchFilter->getResolution() == 'CITY'){
                if (!isset($responseDecoded['default']['geoMapData'])) {
                    throw new GoogleTrendsException(
                        sprintf(
                            'Invalid google response body "%s"...',
                            substr(var_export($responseDecoded, true), 100)
                        )
                    );
                }
                
                if (!isset($row['geoName'], $row['value'], $row['maxValueIndex'])) {
                throw new GoogleTrendsException(
                    sprintf(
                        'Google compared geo list does not contain all keys. Only has: %s',
                        implode(', ', array_keys($row))
                    )
                );
            }
            
                $results[] = new InterestByCityResult(
                    $row['geoName'],
                    (int)($row['value'][0] ?? 0),
                    (int)$row['maxValueIndex'],
                    (bool)($row['hasData'] ?? false),
                    (string)($row['coordinates']['lat'] ?? false),
                    (string)($row['coordinates']['lng'] ?? false)
                );
            }else{
                if (!isset($row['geoCode'], $row['geoName'], $row['value'], $row['maxValueIndex'])) {
                    throw new GoogleTrendsException(
                        sprintf(
                            'Google compared geo list does not contain all keys. Only has: %s',
                            implode(', ', array_keys($row))
                        )
                    );
                }
                
                $results[] = new InterestByRegionResult(
                    $row['geoCode'],
                    $row['geoName'],
                    (int)($row['value'][0] ?? 0),
                    (int)$row['maxValueIndex'],
                    (bool)($row['hasData'] ?? false)
                );
            }

            
        }
        
        if($searchFilter->getResolution() == 'CITY'){
            return new InterestByCityCollection($searchUrl, ...$results);
        }else{
            return new InterestByRegionCollection($searchUrl, ...$results);
        }
    }

    private function buildQuery(SearchFilter $searchFilter): string
    {
        if($searchFilter->getLocation() != ''){
            $geo = ['country' => $searchFilter->getLocation()];
        }else{
            $geo = (object)[];
        }
        
        $request = [
            'geo' => $geo,
            'comparisonItem' => [
                [
                    'time' => $searchFilter->getTime(),
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
            'resolution' => $searchFilter->getResolution(),
            'locale' => $searchFilter->getLanguage(),
            'requestOptions' => [
                'property' => $searchFilter->getSearchType(),
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
