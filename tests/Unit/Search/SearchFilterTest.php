<?php declare(strict_types=1);

namespace GSoares\GoogleTrends\Tests\Unit\Search;

use DateTimeImmutable;
use GSoares\GoogleTrends\Error\GoogleTrendsException;
use GSoares\GoogleTrends\Search\SearchFilter;
use PHPUnit\Framework\TestCase;

class SearchFilterTest extends TestCase
{
    private const CURRENT_DATE = '2010-10-10 00:00:00';

    /**
     * @var SearchFilter
     */
    private $sut;

    public function setUp(): void
    {
        $this->sut = new SearchFilter(new DateTimeImmutable(self::CURRENT_DATE));
    }

    public function test__construct(): void
    {
        $this->assertSame(
            'en-US',
            $this->sut->getLanguage()
        );
        $this->assertSame(
            'US',
            $this->sut->getLocation()
        );
        $this->assertSame(
            '',
            $this->sut->getSearchType()
        );
        $this->assertSame(
            '',
            $this->sut->getSearchTerm()
        );
        $this->assertSame(
            0,
            $this->sut->getCategory()
        );
        $this->assertSame(
            '2010-08-10 2010-09-09',
            $this->sut->getCompareTime()
        );
        $this->assertSame(
            '2010-09-10 2010-10-10',
            $this->sut->getTime()
        );
    }

    public function testWithSearchTerm(): void
    {
        $this->assertSame(
            'test',
            $this->sut->withSearchTerm('test')->getSearchTerm()
        );
    }

    public function testWithToken(): void
    {
        $this->assertSame(
            'token',
            $this->sut->withToken('token')->getToken()
        );
    }

    public function testWithCategory(): void
    {
        $this->assertSame(
            22,
            $this->sut->withCategory(22)->getCategory()
        );
    }

    public function testConsiderGoogleShoppingSearch(): void
    {
        $this->assertSame(
            SearchFilter::SEARCH_SOURCE_GOOGLE_SHOPPING,
            $this->sut->considerGoogleShoppingSearch()->getSearchType()
        );
    }

    public function testConsiderImageSearch(): void
    {
        $this->assertSame(
            SearchFilter::SEARCH_SOURCE_IMAGES,
            $this->sut->considerImageSearch()->getSearchType()
        );
    }

    public function testConsiderWebSearch(): void
    {
        $this->assertSame(
            SearchFilter::SEARCH_SOURCE_WEB,
            $this->sut->considerWebSearch()->getSearchType()
        );
    }

    public function testConsiderNewsSearch(): void
    {
        $this->assertSame(
            SearchFilter::SEARCH_SOURCE_NEWS,
            $this->sut->considerNewsSearch()->getSearchType()
        );
    }

    public function testConsiderYoutubeSearch(): void
    {
        $this->assertSame(
            SearchFilter::SEARCH_SOURCE_YOUTUBE,
            $this->sut->considerYoutubeSearch()->getSearchType()
        );
    }

    public function testWithTopMetrics(): void
    {
        $this->assertSame(
            [
                'TOP'
            ],
            $this->sut->withTopMetrics()->getMetrics()
        );
    }

    public function testWithRisingMetrics(): void
    {
        $this->assertSame(
            [
                'RISING',
            ],
            $this->sut->withRisingMetrics()->getMetrics()
        );
    }

    public function testWithLanguage(): void
    {
        $this->assertSame(
            'pt-BR',
            $this->sut->withLanguage('pt-BR')->getLanguage()
        );
    }

    public function testWithLocation(): void
    {
        $this->assertSame(
            'BR',
            $this->sut->withLocation('BR')->getLocation()
        );
    }

    public function testWithinInterval(): void
    {
        $this->sut->withinInterval(
            (new DateTimeImmutable(self::CURRENT_DATE))->modify('-1 month'),
            new DateTimeImmutable(self::CURRENT_DATE)
        );

        $this->assertSame(
            '2010-08-10 2010-09-09',
            $this->sut->getCompareTime()
        );
        $this->assertSame(
            '2010-09-10 2010-10-10',
            $this->sut->getTime()
        );
    }

    /**
     * @param string $from
     * @param string $to
     *
     * @throws GoogleTrendsException
     *
     * @dataProvider invalidIntervalProvider
     */
    public function testWithinIntervalInvalidWillThrowException(string $from, string $to): void
    {
        $this->expectException(GoogleTrendsException::class);
        $this->expectExceptionMessage(
            sprintf(
                'GoogleTrends error: Invalid interval. From %s to %s',
                $from,
                $to
            )
        );

        $this->sut->withinInterval(new DateTimeImmutable($from), new DateTimeImmutable($to));
    }

    public function invalidIntervalProvider(): array
    {
        return [
            'from is bigger than to' => [
                'from' => '2020-03-27T00:00:00+00:00',
                'to' => '2020-03-26T09:51:10+00:00',
            ],
            'from is equals to to' => [
                'from' => '2020-03-27T00:00:00+00:00',
                'to' => '2020-03-27T00:00:00+00:00',
            ]
        ];
    }
}
