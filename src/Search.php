<?php declare(strict_types=1);

namespace GSoares\GoogleTrends;

use DateTimeImmutable;
use GSoares\GoogleTrends\Error\GoogleTrendsException;
use GSoares\GoogleTrends\Result\KeywordResult;
use GSoares\GoogleTrends\Result\KeywordResultCollection;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use Throwable;

/**
 * @author Gabriel Felipe Soares <gabrielfs7@gmail.com>
 */
class Search
{
    private const DEFAULT_LANG = 'en-US';
    private const DEFAULT_COUNTRY = 'US';
    private const TRENDS_URL = 'https://trends.google.com';

    /**
     * @var ClientInterface
     */
    private $guzzleClient;

    /**
     * @var SearchQueryBuilder
     */
    private $queryBuilder;

    public function __construct(SearchQueryBuilder $queryBuilder = null, ClientInterface $guzzleClient = null)
    {
        $this->guzzleClient = $guzzleClient ?: new Client();
        $this->queryBuilder = $queryBuilder ?: new SearchQueryBuilder();

        if ($queryBuilder === null) {
            $this->queryBuilder
                ->withLanguage(self::DEFAULT_LANG)
                ->withLocation(self::DEFAULT_COUNTRY)
                ->withinInterval((new DateTimeImmutable('now'))->modify('-12 months'), new DateTimeImmutable('now'));
        }
    }

    public function getQueryBuilder(): SearchQueryBuilder
    {
        return $this->queryBuilder;
    }

    /**
     * @return KeywordResultCollection
     *
     * @throws GoogleTrendsException
     */
    public function searchRelatedTerms(): KeywordResultCollection
    {
        $searchUrl = $this->queryBuilder->build();

        try {
            $response = $this->guzzleClient
                ->request('GET', $searchUrl);
        } catch (ClientException $exception) {
            throw new GoogleTrendsException(
                sprintf(
                    'Request error with status code "%s" for url %s',
                    $exception->getResponse()->getStatusCode(),
                    $searchUrl
                ),
                $exception
            );
        } catch (Throwable $exception) {
            throw new GoogleTrendsException(
                $exception->getMessage(),
                $exception
            );
        }

        $responseBody = substr((string)$response->getBody(), 5);
        $responseDecoded = json_decode($responseBody, true);

        if (json_last_error()) {
            throw new GoogleTrendsException(
                sprintf(
                    'JSON parse error "%s" for JSON "%s"',
                    json_last_error_msg(),
                    $responseBody
                )
            );
        }

        if (!isset($responseDecoded['default']['rankedList'])) {
            throw new GoogleTrendsException(
                sprintf(
                    'Invalid google response body "%s"...',
                    substr(var_export($responseDecoded, true), 100)
                )
            );
        }

        $results = [];

        foreach ($responseDecoded['default']['rankedList'] as $row) {
            foreach ($row['rankedKeyword'] ?? [] as $rank) {
                if (!isset($rank['query'], $rank['value'], $rank['link'])) {
                    throw new GoogleTrendsException(
                        sprintf(
                            'Google ranked list does not contain all keys. Only has: %s',
                            implode(', ', array_keys($rank))
                        )
                    );
                }

                $dto = new KeywordResult(
                    (string)$rank['query'],
                    (bool)($rank['hasData'] ?? false),
                    (int)$rank['value'],
                    self::TRENDS_URL . (string)$rank['link']
                );

                $results[] = $dto;
            }
        }

        return new KeywordResultCollection($searchUrl, ...$results);
    }
}
