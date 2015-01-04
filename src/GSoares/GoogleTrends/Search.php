<?php
namespace GSoares\GoogleTrends;

use GSoares\GoogleTrends\Dto\SearchResultDto;
use GSoares\GoogleTrends\Dto\TermDto;
use Guzzle\Http\Client;
use Guzzle\Http\Exception\BadResponseException;

/**
 * @author Gabriel Felipe Soares <gabrielfs7@gmail.com>
 * @package GSoares\GoogleTrends
 */
class Search
{

    const RISING_QUERIES = 'RISING_QUERIES_0_0';
    const TOP_QUERIES = 'TOP_QUERIES_0_0';
    const SEARCH_URL = 'http://www.google.com/trends/fetchComponent';
    const TRENDS_URL = 'http://www.google.com/trends/explore';

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
     * @var Client
     */
    private $guzzleClient;

    /**
     * @var \DateTime
     */
    private $monthInterval;

    /**
     * @var \DateTime
     */
    private $lastDays;

    public function __construct(Client $guzzleClient = null)
    {
        $this->query = [];

        $this->setLanguage('pt-BR')
            ->setLocation('BR')
            ->searchTopQueries()
            ->setMonthInterval((new \DateTime('now'))->modify('-12 months'), new \DateTime('now'));

        $this->guzzleClient = $guzzleClient ?: new Client(self::SEARCH_URL);
    }

    /**
     * @param $initialMonth
     * @param $finalMonth
     * @return $this
     */
    public function setMonthInterval(\DateTime $initialMonth, \DateTime $finalMonth)
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

    /**
     * @param $lastDays
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function setLastDays($lastDays)
    {
        if (!in_array($lastDays, $allowedDays = [7, 30, 90, 365])) {
            throw new \InvalidArgumentException(
                'Allowed days: ' . implode(', ', $allowedDays) .
                '. Supplied: ' . strval($lastDays)
            );
        }

        if ($lastDays == 7) {
            $this->lastDays = 'today+' . intval($lastDays) . '-d';
        }

        if ($lastDays != 7) {
            $this->lastDays = 'today+' . ceil(bcdiv($lastDays, 30)) . '-m';
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function searchRisingQueries()
    {
        $this->cid = self::RISING_QUERIES;

        return $this;
    }

    /**
     * @return $this
     */
    public function searchTopQueries()
    {
        $this->cid = self::TOP_QUERIES;

        return $this;
    }

    /**
     * @param $language
     * @return $this
     */
    public function setLanguage($language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * @param $category
     * @return $this
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @param $location
     * @return $this
     */
    public function setLocation($location)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * @param $word
     * @return $this
     */
    public function addWord($word)
    {
        $this->query[$word] = $word;

        return $this;
    }

    /**
     * @return SearchResultDto
     * @throws \Guzzle\Http\Exception\BadResponseException
     */
    public function search()
    {
        $searchUrl = $this->prepareSearchUrl();
        $response = $this->guzzleClient
            ->setBaseUrl($this->prepareSearchUrl())
            ->get()
            ->send();

        $responseBody = substr($response->getBody(true), 62, -2);

        if (!$responseDecoded = json_decode($responseBody)) {
            throw new BadResponseException(
                'Status code ' . $response->getStatusCode() .
                ': ' . strip_tags($response->getBody(true))
            );
        }

        if ($responseDecoded->status == 'error') {
            $errorMessage = [];

            foreach ($responseDecoded->errors as $error) {
                $errorMessage[] = $error->reason . '. ' . $error->message . '. ' . $error->detailed_message;
            }

            throw new BadResponseException(implode(PHP_EOL, $errorMessage));
        }

        return $this->createDto($responseDecoded, $searchUrl);
    }

    /**
     * @return string
     * @throws \Guzzle\Http\Exception\BadResponseException
     */
    public function searchJson()
    {
        return json_encode($this->search(), JSON_UNESCAPED_SLASHES);
    }

    /**
     * @param \stdClass $responseDecoded
     * @param $searchUrl
     * @return SearchResultDto
     */
    private function createDto(\stdClass $responseDecoded, $searchUrl)
    {
        $searchResult = new SearchResultDto();
        $searchResult->searchUrl = $searchUrl;

        foreach ($responseDecoded->table->rows as $row) {
            $dto = new TermDto();
            $dto->term = $row->c[0]->v;
            $dto->ranking = $row->c[1]->v;
            $dto->trendsUrl = self::TRENDS_URL . $row->c[2]->v;
            $dto->searchUrl = $row->c[3]->v;
            $dto->searchImageUrl = $row->c[3]->v . '&tbm=isch';

            $searchResult->results[] = $dto;
            $searchResult->totalResults++;
        }

        return $searchResult;
    }


    /**
     * @return string
     */
    private function prepareSearchUrl()
    {
        return self::SEARCH_URL .
            '?hl=' . $this->language .
            '&cat=' . $this->category .
            '&geo=' . $this->location .
            '&q=' . implode(',+', $this->query) .
            '&cid=' . $this->cid .
            '&date=' . ($this->lastDays ?: $this->monthInterval) .
            '&cmpt=q&content=1&export=3';
    }
}