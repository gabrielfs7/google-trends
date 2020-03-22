<?php declare(strict_types=1);

namespace GSoares\GoogleTrends;

use DateTimeInterface;
use InvalidArgumentException;

/**
 * @author Gabriel Felipe Soares <gabrielfs7@gmail.com>
 */
class SearchQueryBuilder
{
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
     * @var string
     */
    private $category;

    /**
     * @var string
     */
    private $query;

    /**
     * @var string
     */
    private $metrics;

    /**
     * @var DateTimeInterface
     */
    private $monthInterval;

    /**
     * @var DateTimeInterface
     */
    private $lastDays;

    public function __construct()
    {
        $this->query = [];
        $this->metrics = [];
    }

    public function withToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function withMonthInterval(DateTimeInterface $initialMonth, DateTimeInterface $finalMonth): self
    {
        if ($initialMonth->format('Ym') === $finalMonth->format('Ym')) {
            $this->monthInterval = $initialMonth->format('m/Y');
        }

        if ($initialMonth->format('Ym') !== $finalMonth->format('Ym')) {
            $monthsDifference = ($initialMonth->format('m') - $finalMonth->format('m')) * -1;
            $yearsDifference = ($initialMonth->format('Y') - $finalMonth->format('Y')) * 12;

            $this->monthInterval = $initialMonth->format('m/Y') . '+' . (($yearsDifference - $monthsDifference) * -1) . 'm';
        }

        return $this;
    }

    public function withLastDays(int $lastDays): self
    {
        if (!in_array($lastDays, $allowedDays = [7, 30, 90, 365])) {
            throw new InvalidArgumentException(
                sprintf('Allowed days: %s Supplied: %s',
                    implode(', ', $allowedDays),
                    $lastDays
                )
            );
        }

        if ($lastDays == 7) {
            $this->lastDays = 'today+' . $lastDays . '-d';
        }

        if ($lastDays != 7) {
            $this->lastDays = 'today+' . ceil(bcdiv((string)$lastDays, '30')) . '-m';
        }

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

    public function withCategory(string $category)
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
        $this->query[$word] = $word;

        return $this;
    }

    public function build(): string
    {
        $request = [
            'restriction' => [
                'geo' => [
                    'country' => $this->location,
                ],
                'time' => '2019-03-21+2020-03-21',
                'originalTimeRangeForExploreUrl' => 'today+12-m',
                'complexKeywordsRestriction' => [
                    'keyword' => [
                        [
                            'type' => 'BROAD',
                            'value' => implode(',+', $this->query),
                        ],
                    ],
                ],
            ],
            'keywordType' => 'QUERY',
            'metric' => $this->metrics,
            'trendinessSettings' => [
                'compareTime' => '2018-03-19+2019-03-20',
            ],
            'requestOptions' => [
                'property' => '',
                'backend' => 'IZG',
                'category' => 0,
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
