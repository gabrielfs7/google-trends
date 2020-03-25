<?php declare(strict_types=1);

namespace GSoares\GoogleTrends\Builder;

use DateTimeImmutable;
use DateTimeInterface;
use GSoares\GoogleTrends\Error\GoogleTrendsException;

/**
 * @author Gabriel Felipe Soares <gabrielfs7@gmail.com>
 */
class RelatedSearchUrlBuilder
{
    private const DEFAULT_LAST_DAYS = 7;
    private const DEFAULT_LANG = 'en-US';
    private const DEFAULT_COUNTRY = 'US';
    private const RELATED_SEARCH_URL = 'https://trends.google.com/trends/api/widgetdata/relatedsearches';
    private const ALLOWED_DAYS = [
        7,
        30,
        90,
        365,
    ];

    /**
     * @var string
     */
    private $token;

    /**
     * @var string
     */
    private $location;

    /**
     * @var string
     */
    private $language;

    /**
     * @var int
     */
    private $category;

    /**
     * @var string
     */
    private $searchTerm;

    /**
     * @var string
     */
    private $metrics;

    /**
     * @var string
     */
    private $time;

    /**
     * @var string
     */
    private $compareTime;

    /**
     * @var string
     */
    private $lastDays;

    /**
     * @var string
     */
    private $searchType;

    public function __construct(DateTimeImmutable $currentDate = null)
    {
        $this->searchTerm = [];
        $this->metrics = [];

        $currentDate = $currentDate ?? new DateTimeImmutable();

        $this->withinLastDays(self::DEFAULT_LAST_DAYS)
            ->withLanguage(self::DEFAULT_LANG)
            ->withLocation(self::DEFAULT_COUNTRY)
            ->withinInterval($currentDate->modify('-1 year'), $currentDate)
            ->considerWebSearch();
    }

    public function withToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function withinInterval(DateTimeImmutable $from, DateTimeImmutable $to): self
    {
        $compareFrom = $from->modify('-1 year -2 days');
        $compareTo = $to->modify('-1 year -1 days');

        $this->time = $from->format('Y-m-d') . ' ' . $to->format('Y-m-d');
        $this->compareTime = $compareFrom->format('Y-m-d') . ' ' . $compareTo->format('Y-m-d');

        return $this;
    }

    /**
     * @param int $lastDays
     *
     * @return $this
     *
     * @throws GoogleTrendsException
     */
    public function withinLastDays(int $lastDays): self
    {
        if (!in_array($lastDays, self::ALLOWED_DAYS)) {
            throw new GoogleTrendsException(
                sprintf('Allowed days: %s. Supplied: %s',
                    implode(', ', self::ALLOWED_DAYS),
                    $lastDays
                )
            );
        }

        $pattern = $lastDays === 7
            ? $lastDays . '-d'
            : ceil(bcdiv((string)$lastDays, '30')) . '-m';

        $this->lastDays = 'today ' . $pattern;

        return $this;
    }

    public function considerImageSearch(): self
    {
        $this->searchType = 'images';

        return $this;
    }

    public function considerGoogleShoppingSearch(): self
    {
        $this->searchType = 'frgoogle';

        return $this;
    }

    public function considerYoutubeSearch(): self
    {
        $this->searchType = 'youtube';

        return $this;
    }

    public function considerNewsSearch(): self
    {
        $this->searchType = 'news';

        return $this;
    }

    public function considerWebSearch(): self
    {
        $this->searchType = '';

        return $this;
    }

    public function getSearchType(): string
    {
        return $this->searchType;
    }

    public function withRisingMetrics(): self
    {
        $this->metrics[] = 'RISING';

        return $this;
    }

    public function withTopMetrics(): self
    {
        $this->metrics[] = 'TOP';

        return $this;
    }

    public function withLanguage(string $language): self
    {
        $this->language = $language;

        return $this;
    }

    public function withCategory(int $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function withLocation(string $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function withSearchTerm(string $searchTerm): self
    {
        $this->searchTerm = $searchTerm;

        return $this;
    }

    public function getLocation(): string
    {
        return $this->location;
    }

    public function getLanguage(): string
    {
        return $this->language;
    }

    public function getCategory(): int
    {
        return $this->category;
    }

    public function getLastDays(): string
    {
        return $this->lastDays;
    }

    public function getSearchTerm(): string
    {
        return $this->searchTerm;
    }

    public function build(): string
    {
        $request = [
            'restriction' => [
                'geo' => [
                    'country' => $this->location,
                ],
                'time' => $this->time,
                'originalTimeRangeForExploreUrl' => $this->lastDays,
                'complexKeywordsRestriction' => [
                    'keyword' => [
                        [
                            'type' => 'BROAD',
                            'value' => $this->searchTerm,
                        ],
                    ],
                ],
            ],
            'keywordType' => 'QUERY',
            'metric' => $this->metrics,
            'trendinessSettings' => [
                'compareTime' => $this->compareTime,
            ],
            'requestOptions' => [
                'property' => $this->searchType,
                'backend' => 'IZG',
                'category' => $this->category,
            ],
            'language' => 'en',
        ];

        $query = [
            'hl' => $this->language,
            'tz' => '-60',
            'req' => json_encode($request),
            'token' => $this->token
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
