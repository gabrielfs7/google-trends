<?php declare(strict_types=1);

namespace GSoares\GoogleTrends;

use DateTime;
use DateTimeInterface;
use GSoares\GoogleTrends\Dto\SearchResultDto;
use GSoares\GoogleTrends\Dto\TermDto;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use InvalidArgumentException;

/**
 * @author Gabriel Felipe Soares <gabrielfs7@gmail.com>
 */
class Search
{
    const RISING_QUERIES = 'RISING_QUERIES_0_0';
    const TOP_QUERIES = 'TOP_QUERIES_0_0';
    const RELATED_SEARCH_URL = 'https://trends.google.com/trends/api/widgetdata/relatedsearches';
    const TRENDS_URL = 'https://trends.google.com';

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
    private $cid;

    /**
     * @var ClientInterface
     */
    private $guzzleClient;

    /**
     * @var DateTimeInterface
     */
    private $monthInterval;

    /**
     * @var DateTimeInterface
     */
    private $lastDays;

    public function __construct(ClientInterface $guzzleClient = null)
    {
        $this->query = [];
        $this->guzzleClient = $guzzleClient ?: new Client();

        $this->setLanguage('pt-BR')
            ->setLocation('BR')
            ->searchTopQueries()
            ->setMonthInterval((new DateTime('now'))->modify('-12 months'), new DateTime('now'));
    }

    public function setMonthInterval(DateTimeInterface $initialMonth, DateTimeInterface $finalMonth): self
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

    public function setLastDays(int $lastDays): self
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

    public function searchRisingQueries(): self
    {
        $this->cid = self::RISING_QUERIES;

        return $this;
    }

    public function searchTopQueries(): self
    {
        $this->cid = self::TOP_QUERIES;

        return $this;
    }

    public function setLanguage(string $language): self
    {
        $this->language = $language;

        return $this;
    }

    public function setCategory(string $category)
    {
        $this->category = $category;

        return $this;
    }

    public function setLocation(string $location)
    {
        $this->location = $location;

        return $this;
    }

    public function addWord(string $word)
    {
        $this->query[$word] = $word;

        return $this;
    }

    public function searchRelatedTerms(): SearchResultDto
    {
        $searchUrl = $this->prepareSearchUrl();
        $response = $this->guzzleClient
            ->request('GET', $this->prepareSearchUrl());

        //@FIXME @TODO $responseBody = substr($response->getBody(true), 5);
        //@TODO Discover how to pass the parameters
        //@TODO Discover how regenerate the token

        $responseBody = '{"default":{"rankedList":[{"rankedKeyword":[{"query":"hair salon","value":100,"formattedValue":"100","hasData":true,"link":"/trends/explore?q\\u003dhair+salon\\u0026date\\u003dtoday+12-m\\u0026geo\\u003dUS"},{"query":"hair color","value":70,"formattedValue":"70","hasData":true,"link":"/trends/explore?q\\u003dhair+color\\u0026date\\u003dtoday+12-m\\u0026geo\\u003dUS"},{"query":"long hair","value":61,"formattedValue":"61","hasData":true,"link":"/trends/explore?q\\u003dlong+hair\\u0026date\\u003dtoday+12-m\\u0026geo\\u003dUS"},{"query":"short hair","value":61,"formattedValue":"61","hasData":true,"link":"/trends/explore?q\\u003dshort+hair\\u0026date\\u003dtoday+12-m\\u0026geo\\u003dUS"},{"query":"black hair","value":58,"formattedValue":"58","hasData":true,"link":"/trends/explore?q\\u003dblack+hair\\u0026date\\u003dtoday+12-m\\u0026geo\\u003dUS"},{"query":"curly hair","value":49,"formattedValue":"49","hasData":true,"link":"/trends/explore?q\\u003dcurly+hair\\u0026date\\u003dtoday+12-m\\u0026geo\\u003dUS"},{"query":"hair dye","value":47,"formattedValue":"47","hasData":true,"link":"/trends/explore?q\\u003dhair+dye\\u0026date\\u003dtoday+12-m\\u0026geo\\u003dUS"},{"query":"blonde hair","value":40,"formattedValue":"40","hasData":true,"link":"/trends/explore?q\\u003dblonde+hair\\u0026date\\u003dtoday+12-m\\u0026geo\\u003dUS"},{"query":"brown hair","value":38,"formattedValue":"38","hasData":true,"link":"/trends/explore?q\\u003dbrown+hair\\u0026date\\u003dtoday+12-m\\u0026geo\\u003dUS"},{"query":"hair cut","value":35,"formattedValue":"35","hasData":true,"link":"/trends/explore?q\\u003dhair+cut\\u0026date\\u003dtoday+12-m\\u0026geo\\u003dUS"},{"query":"hair styles","value":34,"formattedValue":"34","hasData":true,"link":"/trends/explore?q\\u003dhair+styles\\u0026date\\u003dtoday+12-m\\u0026geo\\u003dUS"},{"query":"hair salons","value":33,"formattedValue":"33","hasData":true,"link":"/trends/explore?q\\u003dhair+salons\\u0026date\\u003dtoday+12-m\\u0026geo\\u003dUS"},{"query":"red hair","value":31,"formattedValue":"31","hasData":true,"link":"/trends/explore?q\\u003dred+hair\\u0026date\\u003dtoday+12-m\\u0026geo\\u003dUS"},{"query":"hair loss","value":28,"formattedValue":"28","hasData":true,"link":"/trends/explore?q\\u003dhair+loss\\u0026date\\u003dtoday+12-m\\u0026geo\\u003dUS"},{"query":"hairstyles","value":28,"formattedValue":"28","hasData":true,"link":"/trends/explore?q\\u003dhairstyles\\u0026date\\u003dtoday+12-m\\u0026geo\\u003dUS"},{"query":"hair removal","value":23,"formattedValue":"23","hasData":true,"link":"/trends/explore?q\\u003dhair+removal\\u0026date\\u003dtoday+12-m\\u0026geo\\u003dUS"},{"query":"hair growth","value":23,"formattedValue":"23","hasData":true,"link":"/trends/explore?q\\u003dhair+growth\\u0026date\\u003dtoday+12-m\\u0026geo\\u003dUS"},{"query":"blue hair","value":21,"formattedValue":"21","hasData":true,"link":"/trends/explore?q\\u003dblue+hair\\u0026date\\u003dtoday+12-m\\u0026geo\\u003dUS"},{"query":"hair salon near me","value":20,"formattedValue":"20","hasData":true,"link":"/trends/explore?q\\u003dhair+salon+near+me\\u0026date\\u003dtoday+12-m\\u0026geo\\u003dUS"},{"query":"salon near me","value":20,"formattedValue":"20","hasData":true,"link":"/trends/explore?q\\u003dsalon+near+me\\u0026date\\u003dtoday+12-m\\u0026geo\\u003dUS"},{"query":"white hair","value":19,"formattedValue":"19","hasData":true,"link":"/trends/explore?q\\u003dwhite+hair\\u0026date\\u003dtoday+12-m\\u0026geo\\u003dUS"},{"query":"hair extensions","value":18,"formattedValue":"18","hasData":true,"link":"/trends/explore?q\\u003dhair+extensions\\u0026date\\u003dtoday+12-m\\u0026geo\\u003dUS"},{"query":"hair store","value":18,"formattedValue":"18","hasData":true,"link":"/trends/explore?q\\u003dhair+store\\u0026date\\u003dtoday+12-m\\u0026geo\\u003dUS"},{"query":"hair dryer","value":17,"formattedValue":"17","hasData":true,"link":"/trends/explore?q\\u003dhair+dryer\\u0026date\\u003dtoday+12-m\\u0026geo\\u003dUS"},{"query":"hair cuts","value":17,"formattedValue":"17","hasData":true,"link":"/trends/explore?q\\u003dhair+cuts\\u0026date\\u003dtoday+12-m\\u0026geo\\u003dUS"}]},{"rankedKeyword":[{"query":"passion twist hair","value":1050,"formattedValue":"+1,050%","link":"/trends/explore?q\\u003dpassion+twist+hair\\u0026date\\u003dtoday+12-m\\u0026geo\\u003dUS"},{"query":"hair love short film","value":1050,"formattedValue":"+1,050%","link":"/trends/explore?q\\u003dhair+love+short+film\\u0026date\\u003dtoday+12-m\\u0026geo\\u003dUS"},{"query":"sir hair down","value":750,"formattedValue":"+750%","link":"/trends/explore?q\\u003dsir+hair+down\\u0026date\\u003dtoday+12-m\\u0026geo\\u003dUS"},{"query":"overtone hair color","value":400,"formattedValue":"+400%","link":"/trends/explore?q\\u003dovertone+hair+color\\u0026date\\u003dtoday+12-m\\u0026geo\\u003dUS"},{"query":"overtone","value":300,"formattedValue":"+300%","link":"/trends/explore?q\\u003dovertone\\u0026date\\u003dtoday+12-m\\u0026geo\\u003dUS"},{"query":"luvme hair","value":250,"formattedValue":"+250%","link":"/trends/explore?q\\u003dluvme+hair\\u0026date\\u003dtoday+12-m\\u0026geo\\u003dUS"},{"query":"spring twist hair","value":100,"formattedValue":"+100%","link":"/trends/explore?q\\u003dspring+twist+hair\\u0026date\\u003dtoday+12-m\\u0026geo\\u003dUS"},{"query":"west kiss hair","value":100,"formattedValue":"+100%","link":"/trends/explore?q\\u003dwest+kiss+hair\\u0026date\\u003dtoday+12-m\\u0026geo\\u003dUS"},{"query":"hair paint wax","value":100,"formattedValue":"+100%","link":"/trends/explore?q\\u003dhair+paint+wax\\u0026date\\u003dtoday+12-m\\u0026geo\\u003dUS"},{"query":"isee hair","value":80,"formattedValue":"+80%","link":"/trends/explore?q\\u003disee+hair\\u0026date\\u003dtoday+12-m\\u0026geo\\u003dUS"},{"query":"ariana grande natural hair","value":80,"formattedValue":"+80%","link":"/trends/explore?q\\u003dariana+grande+natural+hair\\u0026date\\u003dtoday+12-m\\u0026geo\\u003dUS"},{"query":"revlon hair dryer","value":60,"formattedValue":"+60%","link":"/trends/explore?q\\u003drevlon+hair+dryer\\u0026date\\u003dtoday+12-m\\u0026geo\\u003dUS"},{"query":"revlon one step hair dryer","value":60,"formattedValue":"+60%","link":"/trends/explore?q\\u003drevlon+one+step+hair+dryer\\u0026date\\u003dtoday+12-m\\u0026geo\\u003dUS"},{"query":"hair crimper","value":50,"formattedValue":"+50%","link":"/trends/explore?q\\u003dhair+crimper\\u0026date\\u003dtoday+12-m\\u0026geo\\u003dUS"},{"query":"madison reed hair color","value":50,"formattedValue":"+50%","link":"/trends/explore?q\\u003dmadison+reed+hair+color\\u0026date\\u003dtoday+12-m\\u0026geo\\u003dUS"},{"query":"arctic fox hair dye","value":50,"formattedValue":"+50%","link":"/trends/explore?q\\u003darctic+fox+hair+dye\\u0026date\\u003dtoday+12-m\\u0026geo\\u003dUS"},{"query":"hair galleria","value":40,"formattedValue":"+40%","link":"/trends/explore?q\\u003dhair+galleria\\u0026date\\u003dtoday+12-m\\u0026geo\\u003dUS"},{"query":"at home laser hair removal","value":40,"formattedValue":"+40%","link":"/trends/explore?q\\u003dat+home+laser+hair+removal\\u0026date\\u003dtoday+12-m\\u0026geo\\u003dUS"}]}]}}';

        $responseDecoded = json_decode($responseBody, true);

        if (json_last_error()) {
            throw new InvalidArgumentException(
                sprintf(
                    'Status code %s: %s',
                    $response->getStatusCode(),
                    strip_tags((string)$response->getBody())
                )
            );
        }

        //@TODO @FIXME Properly handle the error
//        if ($responseDecoded->status == 'error') {
//            $errorMessage = [];
//
//            foreach ($responseDecoded->errors as $error) {
//                $errorMessage[] = $error->reason . '. ' . $error->message . '. ' . $error->detailed_message;
//            }
//
//            throw new BadResponseException(implode(PHP_EOL, $errorMessage));
//        }

        return $this->createDto($responseDecoded, $searchUrl);
    }

    private function createDto(array $responseDecoded, $searchUrl): SearchResultDto
    {
        $results = [];

        $rows = $responseDecoded['default']['rankedList'] ?? [];

        foreach ($rows as $row) {
            foreach ($row['rankedKeyword'] ?? [] as $rank) {
                $dto = new TermDto(
                    $rank['query'] ?? '',
                    $rank['hasData'] ?? false,
                    $rank['value'] ?? 0,
                    self::TRENDS_URL . ($rank['link'] ?? '')
                );

                $results[] = $dto;
            }
        }

        return new SearchResultDto($searchUrl, ...$results);
    }

    private function prepareSearchUrl(): string
    {
        //@TODO @FIXME Swith to dynamic created URL
        return 'https://trends.google.com/trends/api/widgetdata/relatedsearches?hl=en-US&tz=-60&req=%7B%22restriction%22:%7B%22geo%22:%7B%22country%22:%22US%22%7D,%22time%22:%222019-03-21+2020-03-21%22,%22originalTimeRangeForExploreUrl%22:%22today+12-m%22,%22complexKeywordsRestriction%22:%7B%22keyword%22:%5B%7B%22type%22:%22BROAD%22,%22value%22:%22hair%22%7D%5D%7D%7D,%22keywordType%22:%22QUERY%22,%22metric%22:%5B%22TOP%22,%22RISING%22%5D,%22trendinessSettings%22:%7B%22compareTime%22:%222018-03-19+2019-03-20%22%7D,%22requestOptions%22:%7B%22property%22:%22%22,%22backend%22:%22IZG%22,%22category%22:0%7D,%22language%22:%22en%22%7D&token=APP6_UEAAAAAXneuaIAq6nfqclDVeiVOpitMIpP1x-BQ';

        return self::RELATED_SEARCH_URL .
            '?hl=' . $this->language .
            '&cat=' . $this->category .
            '&geo=' . $this->location .
            '&q=' . implode(',+', $this->query) .
            '&cid=' . $this->cid .
            '&date=' . ($this->lastDays ?: $this->monthInterval) .
            '&cmpt=q&content=1&export=3';
    }
}
