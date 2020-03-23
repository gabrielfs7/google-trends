<?php declare(strict_types=1);

namespace GSoares\GoogleTrends\Search;

use GSoares\GoogleTrends\Error\GoogleTrendsException;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\ResponseInterface;
use Throwable;

/**
 * @author Gabriel Felipe Soares <gabrielfs7@gmail.com>
 */
class SearchRequest
{
    private const MAX_TRIES = 5;

    /**
     * @var ClientInterface
     */
    private $guzzleClient;

    /**
     * @var int
     */
    private $totalTries;

    /**
     * @param string $searchUrl
     * @return array
     *
     * @throws GoogleTrendsException
     */
    public function search(string $searchUrl): array
    {
        try {
            $response = $this->doSearch($searchUrl);
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
        } finally {
            $this->totalTries = 0;
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

        return $responseDecoded;
    }

    public function __construct(ClientInterface $guzzleClient = null)
    {
        $this->guzzleClient = $guzzleClient ?: new Client();
        $this->totalTries = 0;
    }

    /**
     * @param string $searchUrl
     * @param array $options
     *
     * @return ResponseInterface
     *
     * @throws GuzzleException
     */
    private function doSearch(string $searchUrl, array $options = []): ResponseInterface
    {
        try {
            $this->totalTries++;

            return $this->guzzleClient
                ->request('GET', $searchUrl, $options);
        } catch (ClientException $exception) {
            if ($exception->getResponse()->getStatusCode() === 429 && $this->totalTries < self::MAX_TRIES) {
                return $this->doSearch(
                    $searchUrl,
                    [
                        RequestOptions::HEADERS => [
                            'cookie' => $this->getRefreshedCookieHeader($exception)
                        ]
                    ]
                );
            }

            throw $exception;
        }
    }

    private function getRefreshedCookieHeader(ClientException $exception): string
    {
        return (string)explode(
            ';',
            $exception->getResponse()->getHeaders()['Set-Cookie'][0] ?? []
        )[0];
    }
}
