<?php declare(strict_types=1);

namespace GSoares\GoogleTrends\Tests\Integration\Search;

use DateTimeImmutable;
use GSoares\GoogleTrends\Error\GoogleTrendsException;
use GSoares\GoogleTrends\Result\ExploreResult;
use GSoares\GoogleTrends\Result\ExploreResultCollection;
use GSoares\GoogleTrends\Result\RelatedResult;
use GSoares\GoogleTrends\Result\RelatedResultCollection;
use GSoares\GoogleTrends\Search\ExploreSearch;
use GSoares\GoogleTrends\Search\RelatedTopicsSearch;
use GSoares\GoogleTrends\Search\SearchFilter;
use GSoares\GoogleTrends\Search\SearchRequest;
use PHPUnit\Framework\TestCase;

class RelatedTopicsSearchTest extends TestCase
{
    private const SEARCH_URL = 'https://trends.google.com/trends/api/widgetdata/relatedsearches?hl=en-US&tz=-60&req=%7B%22restriction%22:%7B%22geo%22:%7B%22country%22:%22US%22%7D,%22time%22:%222010-09-10+2010-10-10%22,%22originalTimeRangeForExploreUrl%22:%222010-09-10+2010-10-10%22,%22complexKeywordsRestriction%22:%7B%22keyword%22:%5B%7B%22type%22:%22BROAD%22,%22value%22:%22%22%7D%5D%7D%7D,%22keywordType%22:%22ENTITY%22,%22metric%22:%5B%5D,%22trendinessSettings%22:%7B%22compareTime%22:%222010-08-10+2010-09-09%22%7D,%22requestOptions%22:%7B%22property%22:%22%22,%22backend%22:%22IZG%22,%22category%22:0%7D,%22language%22:%22en%22%7D&token=TOKEN';

    /**
     * @var SearchRequest
     */
    private $searchRequest;

    /**
     * @var ExploreSearch
     */
    private $exploreSearch;

    /**
     * @var SearchFilter
     */
    private $searchFilter;

    /**
     * @var RelatedTopicsSearch
     */
    private $sut;

    public function setUp(): void
    {
        $this->searchFilter = new SearchFilter(new DateTimeImmutable('2010-10-10 00:00:00'));
        $this->searchRequest = $this->createMock(SearchRequest::class);
        $this->exploreSearch = $this->createMock(ExploreSearch::class);
        $this->sut = new RelatedTopicsSearch($this->exploreSearch, $this->searchRequest);

        $this->exploreSearch
            ->expects($this->once())
            ->method('search')
            ->with($this->searchFilter)
            ->willReturn(
                new ExploreResultCollection(
                    new ExploreResult('RELATED_TOPICS', 'TOKEN')
                )
            );
    }

    public function testSearchWillReturnResult(): void
    {
        $relatedQueriesResult = new RelatedResult(
            'Title - Topic',
            true,
            100,
            'https://trends.google.com/link'
        );

        $this->searchRequest
            ->expects($this->once())
            ->method('search')
            ->with(self::SEARCH_URL)
            ->willReturn(
                [
                    'default' => [
                        'rankedList' => [
                            [
                                'rankedKeyword' => [
                                    [
                                        'topic' => [
                                            'title' => 'Title',
                                            'type' => 'Topic'
                                        ],
                                        'value' => 100,
                                        'link' => '/link',
                                        'hasData' => true,
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            );

        $this->assertEquals(
            new RelatedResultCollection(self::SEARCH_URL, $relatedQueriesResult),
            $this->sut->search($this->searchFilter)
        );
    }

    public function testSearchWillThrowExceptionWhenMissingRequiredKeys(): void
    {
        $this->searchRequest
            ->expects($this->once())
            ->method('search')
            ->with(self::SEARCH_URL)
            ->willReturn(
                [
                    'default' => [
                        'rankedList' => [
                            [
                                'rankedKeyword' => [
                                    [
                                        'a' => ''
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            );

        $this->expectException(GoogleTrendsException::class);
        $this->expectExceptionMessage('GoogleTrends error: Google ranked list does not contain all keys. Only has: a');

        $this->sut->search($this->searchFilter);
    }

    public function testSearchWillThrowExceptionWhenInvalidResult(): void
    {
        $this->searchRequest
            ->expects($this->once())
            ->method('search')
            ->with(self::SEARCH_URL)
            ->willReturn(
                [
                    'a' => []
                ]
            );

        $this->expectException(GoogleTrendsException::class);
        $this->expectExceptionMessage('GoogleTrends error: Invalid google response body ""');

        $this->sut->search($this->searchFilter);
    }
}
