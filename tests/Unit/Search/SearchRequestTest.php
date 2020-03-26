<?php declare(strict_types=1);

namespace GSoares\GoogleTrends\Tests\Unit\Search;

use GSoares\GoogleTrends\Error\GoogleTrendsException;
use GSoares\GoogleTrends\Search\SearchRequest;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\RequestOptions;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class SearchRequestTest extends TestCase
{
    private const SEARCH_URL = 'http://does-not-matter';
    private const RESPONSE_JSON = ')]}\',{}';

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var SearchRequest
     */
    private $sut;

    public function setUp(): void
    {
        $this->client = $this->createMock(ClientInterface::class);
        $this->sut = new SearchRequest($this->client);
    }

    public function testSearchWillReturnResult(): void
    {
        $response = $this->createMock(ResponseInterface::class);

        $response->expects($this->once())
            ->method('getBody')
            ->willReturn(self::RESPONSE_JSON);

        $this->client
            ->expects($this->once())
            ->method('request')
            ->with('GET', self::SEARCH_URL, [])
            ->willReturn($response);

        $this->assertSame(
            [],
            $this->sut->search(self::SEARCH_URL)
        );
    }

    public function testSearchWillReturnInvalidJson(): void
    {
        $response = $this->createMock(ResponseInterface::class);

        $response->expects($this->once())
            ->method('getBody')
            ->willReturn('<html></html>');

        $this->client
            ->expects($this->once())
            ->method('request')
            ->with('GET', self::SEARCH_URL, [])
            ->willReturn($response);

        $this->expectException(GoogleTrendsException::class);
        $this->expectExceptionMessage('GoogleTrends error: JSON parse error "Syntax error" for JSON "></html>"');

        $this->sut->search(self::SEARCH_URL);
    }

    public function testSearchWillThrowClientException(): void
    {
        $request = $this->createMock(RequestInterface::class);
        $response = $this->createMock(ResponseInterface::class);
        $response->expects($this->any())
            ->method('getStatusCode')
            ->willReturn(400);

        $this->client
            ->expects($this->once())
            ->method('request')
            ->with('GET', self::SEARCH_URL, [])
            ->willThrowException(
                new ClientException('ERROR', $request, $response)
            );

        $this->expectException(GoogleTrendsException::class);
        $this->expectExceptionMessage(
            sprintf(
                'GoogleTrends error: Request error with status code "400" for url "%s"',
                self::SEARCH_URL
            )
        );

        $this->sut->search(self::SEARCH_URL);
    }

    public function testWillRetryWhenSearchSendStatusCode429(): void
    {
        $request = $this->createMock(RequestInterface::class);
        $response = $this->createMock(ResponseInterface::class);
        $response->expects($this->any())
            ->method('getStatusCode')
            ->willReturn(429);
        $response->expects($this->any())
            ->method('getHeaders')
            ->willReturn(
                [
                    'Set-Cookie' => [
                        'test_cookie;other thing;another thing;'
                    ]
                ]
            );

        $this->client
            ->expects($this->at(0))
            ->method('request')
            ->with('GET', self::SEARCH_URL, [])
            ->willThrowException(
                new ClientException('ERROR', $request, $response)
            );

        $this->client
            ->expects($this->at(1))
            ->method('request')
            ->with(
                'GET',
                self::SEARCH_URL,
                [
                    RequestOptions::HEADERS => [
                        'cookie' => 'test_cookie'
                    ]
                ]
            )
            ->willThrowException(
                new ClientException('ERROR', $request, $response)
            );

        $this->expectException(GoogleTrendsException::class);
        $this->expectExceptionMessage(
            sprintf(
                'GoogleTrends error: Request error with status code "429" for url "%s"',
                self::SEARCH_URL
            )
        );

        $this->sut->setMaxTries(2)->search(self::SEARCH_URL);
    }
}
