<?php declare(strict_types=1);

namespace GSoares\GoogleTrends\Search;

use DateTimeImmutable;
use GSoares\GoogleTrends\Error\GoogleTrendsException;

/**
 * @author Gabriel Felipe Soares <gabrielfs7@gmail.com>
 */
class SearchFilter
{
    private const DEFAULT_LANG = 'en-US';
    private const DEFAULT_COUNTRY = 'US';

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
     * @var string[]
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
    private $searchType;

    /**
     * @var string
     */
    private $keywordType;

    /**
     * @var DateTimeImmutable
     */
    private $currentDate;

    public function __construct(DateTimeImmutable $currentDate = null)
    {
        $this->metrics = [];
        $this->searchTerm = '';
        $this->category = 0;
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

    /**
     * @param DateTimeImmutable $from
     * @param DateTimeImmutable $to
     *
     * @return $this
     *
     * @throws GoogleTrendsException
     */
    public function withinInterval(DateTimeImmutable $from, DateTimeImmutable $to): self
    {
        if ($from >= $to || $from->format('Ymd') === $to->format('Ymd')) {
            throw new GoogleTrendsException(
                sprintf(
                    'Invalid interval. From %s to %s',
                    $from->format(DATE_ATOM),
                    $to->format(DATE_ATOM)
                )
            );
        }

        $from = $from->setTime(0, 0, 0);
        $to = $to->setTime(23, 59, 50);

        $this->time = $from->format('Y-m-d') . ' ' . $to->format('Y-m-d');

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

    public function getToken(): string
    {
        return (string)$this->token;
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

    public function getSearchTerm(): string
    {
        return $this->searchTerm;
    }

    public function getSearchType(): string
    {
        return $this->searchType;
    }

    public function getTime(): string
    {
        return $this->time;
    }

    public function getCompareTime(): string
    {
        return $this->compareTime;
    }

    public function getMetrics(): array
    {
        return $this->metrics;
    }

    public function isConsideringTopMetrics(): bool
    {
        return in_array('TOP', $this->metrics);
    }

    public function isConsideringRisingMetrics(): bool
    {
        return in_array('RISING', $this->metrics);
    }
}
