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
    private $terms;

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

    public function __construct()
    {
        $this->terms = [];
        $this->metrics = [];

        $this->withinLastDays(self::DEFAULT_LAST_DAYS)
            ->withLanguage(self::DEFAULT_LANG)
            ->withLocation(self::DEFAULT_COUNTRY)
            ->withinInterval((new DateTimeImmutable())->modify('-1 year'), new DateTimeImmutable());
    }

    public function withToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function withinInterval(DateTimeInterface $from, DateTimeInterface $to): self
    {
        $compareFrom = (new DateTimeImmutable($from->format('Y-m-d H:i:s')))->modify('-1 year -2 days');
        $compareTo = (new DateTimeImmutable($to->format('Y-m-d H:i:s')))->modify('-1 year -1 days');

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

    public function withCategory(int $category)
    {
        $this->category = $category;

        return $this;
    }

    public function withLocation(string $location)
    {
        $this->location = $location;

        return $this;
    }

    public function withWord(string $word)
    {
        $this->terms[$word] = $word;

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

    public function getSearchTerms(): string
    {
        return implode(',', $this->terms);
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
                            'value' => $this->getSearchTerms(),
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
                'property' => '',
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
