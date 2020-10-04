<?php declare(strict_types=1);

namespace GSoares\GoogleTrends\Tests\Unit\Search\Psr7;

use DateTimeImmutable;
use GSoares\GoogleTrends\Error\GoogleTrendsException;
use GSoares\GoogleTrends\Result\RelatedResultCollection;
use GSoares\GoogleTrends\Search\Psr7\Search;
use GSoares\GoogleTrends\Search\SearchFilter;
use GSoares\GoogleTrends\Search\SearchInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

class SearchTest extends TestCase
{
    /**
     * @var SearchFilter
     */
    private $sut;

    /**
     * @var SearchInterface|MockObject
     */
    private $searchRequest;

    /**
     * @var ServerRequestInterface|MockObject
     */
    private $request;

    public function setUp(): void
    {
        $this->request = $this->createMock(ServerRequestInterface::class);
        $this->searchRequest = $this->createMock(SearchInterface::class);
        $this->sut = new Search($this->searchRequest);
    }

    public function testSearch(): void
    {
        $result = new RelatedResultCollection('searchUrl', ...[]);

        $this->request
            ->expects($this->once())
            ->method('getQueryParams')
            ->willReturn(
                [
                    'categoryId' => '1',
                    'searchTerm' => 'sports',
                    'location' => 'BR',
                    'intervalFrom' => '2010-10-10 00:00:00',
                    'intervalTo' => '2010-10-11 00:00:00',
                    'language' => 'pt-BR',
                    'searchSource' => SearchFilter::SEARCH_SOURCE_YOUTUBE,
                    'withTopMetrics' => '1',
                    'withRisingMetrics' => '1'
                ]
            );

        $this->searchRequest
            ->expects($this->once())
            ->method('search')
            ->willReturnCallback(
                function (SearchFilter $searchFilter) use ($result) {
                    $this->assertSame('pt-BR', $searchFilter->getLanguage());
                    $this->assertSame('sports', $searchFilter->getSearchTerm());
                    $this->assertSame(1, $searchFilter->getCategory());
                    $this->assertSame('BR', $searchFilter->getLocation());
                    $this->assertSame('2010-10-08 2010-10-09', $searchFilter->getCompareTime());
                    $this->assertSame(SearchFilter::SEARCH_SOURCE_YOUTUBE, $searchFilter->getSearchType());
                    $this->assertSame(
                        [
                            'TOP',
                            'RISING',
                        ],
                        $searchFilter->getMetrics()
                    );

                    return $result;
                }
            );

        $response = $this->sut->search($this->request);

        $this->assertSame('application/json', current($response->getHeader('Content-Type')));
        $this->assertSame(json_encode($result->jsonSerialize()), (string)$response->getBody());
    }

    /**
     * @dataProvider searchSourceProvider
     */
    public function testSearchDifferentSources(string $source): void
    {
        $this->request
            ->expects($this->once())
            ->method('getQueryParams')
            ->willReturn(
                [
                    'searchSource' => $source,
                ]
            );

        $this->searchRequest
            ->expects($this->once())
            ->method('search')
            ->willReturnCallback(
                function (SearchFilter $searchFilter) use ($source) {
                    $this->assertSame($source, $searchFilter->getSearchType());

                    return new RelatedResultCollection('searchUrl', ...[]);
                }
            );

        $this->sut->search($this->request);
    }

    public function searchSourceProvider(): array
    {
        return [
            [
                SearchFilter::SEARCH_SOURCE_YOUTUBE,
            ],
            [
                SearchFilter::SEARCH_SOURCE_IMAGES,
            ],
            [
                SearchFilter::SEARCH_SOURCE_GOOGLE_SHOPPING,
            ],
            [
                SearchFilter::SEARCH_SOURCE_NEWS,
            ],
            [
                SearchFilter::SEARCH_SOURCE_WEB,
            ],
            [
                'will-fallback-to-web',
            ],
        ];
    }

    public function testSearchWithError(): void
    {
        $this->searchRequest
            ->expects($this->once())
            ->method('search')
            ->willThrowException(new GoogleTrendsException('Custom message'));

        $response = $this->sut->search($this->request);

        $this->assertSame('application/json', current($response->getHeader('Content-Type')));
        $this->assertSame(
            json_encode(
                [
                    'errors' => [
                        [
                            'status' => 400,
                            'code' => 19980904,
                            'title' => 'GoogleTrends error: Custom message',
                            'details' => 'GoogleTrends error: Custom message',
                        ]
                    ]
                ]
            ),
            (string)$response->getBody()
        );
    }
}
