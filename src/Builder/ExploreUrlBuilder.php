<?php declare(strict_types=1);

namespace GSoares\GoogleTrends\Builder;

/**
 * @author Gabriel Felipe Soares <gabrielfs7@gmail.com>
 */
class ExploreUrlBuilder
{
    private const EXPLORE_URL = 'https://trends.google.com/trends/api/explore';

    /**
     * @var RelatedSearchUrlBuilder
     */
    private $searchUrlBuilder;

    public function __construct(RelatedSearchUrlBuilder $searchUrlBuilder)
    {
        $this->searchUrlBuilder = $searchUrlBuilder;
    }

    public function build(): string
    {
        $request = [
            'comparisonItem' => [
                [
                    'keyword' => $this->searchUrlBuilder->getSearchTerm(),
                    'geo' => $this->searchUrlBuilder->getLocation(),
                    'time' => $this->searchUrlBuilder->getLastDays()
                ]
            ],
            'category' => $this->searchUrlBuilder->getCategory(),
            'property' => $this->searchUrlBuilder->getSearchType(),
        ];

        $query = [
            'hl' => $this->searchUrlBuilder->getLanguage(),
            'tz' => '-60',
            'req' => json_encode($request),
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

        return self::EXPLORE_URL . '?' . $queryString;
    }
}
