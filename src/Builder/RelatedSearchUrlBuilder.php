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
    private const DEFAULT_LANG = 'en-US';
    private const DEFAULT_COUNTRY = 'US';
    private const RELATED_SEARCH_URL = 'https://trends.google.com/trends/api/widgetdata/relatedsearches';

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
    private $originalTimeRangeForExploreUrl;

    /**
     * @var string
     */
    private $searchType;

    /** @var DateTimeImmutable */
    private $currentDate;

    public function __construct(DateTimeImmutable $currentDate = null)
    {
        $this->searchTerm = [];
        $this->metrics = [];

        $this->currentDate = $currentDate ?? new DateTimeImmutable();

        $this->withinInterval($this->currentDate->modify('-1 month'), $this->currentDate)
            ->withLanguage(self::DEFAULT_LANG)
            ->withLocation(self::DEFAULT_COUNTRY)
            ->considerWebSearch();
    }

    public function withToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function withinInterval(DateTimeImmutable $from, DateTimeImmutable $to): self
    {
        if ($from >= $to || $from->format('Ymd') === $to->format('Ymd')) {
            throw new GoogleTrendsException(
                sprintf('Invalid interval. From %s to %s',
                    $from->format(DATE_ATOM),
                    $to->format(DATE_ATOM)
                )
            );
        }

        $from = $from->setTime(0, 0, 0);
        $to = $to->setTime(23, 59, 50);

        $this->time = $from->format('Y-m-d') . ' ' . $to->format('Y-m-d');
        $this->originalTimeRangeForExploreUrl = $this->time;

        $daysDifference = (int)ceil(($to->getTimestamp() - $from->getTimestamp()) / 60 / 60 / 24);

        $this->compareTime = $from->modify('-' . $daysDifference . ' days')
                ->format('Y-m-d')
            . ' '
            . $to->modify('-' . $daysDifference . ' days')
                ->format('Y-m-d');

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

    public function getOriginalTimeRangeForExploreUrl(): string
    {
        return $this->originalTimeRangeForExploreUrl;
    }

    public function getSearchTerm(): string
    {
        return $this->searchTerm;
    }

    public function getSearchType(): string
    {
        return $this->searchType;
    }

    public function build(): string
    {
        $request = [
            'restriction' => [
                'geo' => [
                    'country' => $this->location,
                ],
                'time' => $this->time,
                'originalTimeRangeForExploreUrl' => $this->originalTimeRangeForExploreUrl,
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
