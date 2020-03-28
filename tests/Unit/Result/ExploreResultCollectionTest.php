<?php declare(strict_types=1);

namespace GSoares\GoogleTrends\Tests\Unit\Result;

use GSoares\GoogleTrends\Error\GoogleTrendsException;
use GSoares\GoogleTrends\Result\ExploreResult;
use GSoares\GoogleTrends\Result\ExploreResultCollection;
use PHPUnit\Framework\TestCase;

class ExploreResultCollectionTest extends TestCase
{
    public function testCanGetResults(): void
    {
        $topicsResult = new ExploreResult('RELATED_TOPICS', 'TOKEN');
        $queriesResult = new ExploreResult('RELATED_QUERIES', 'TOKEN');

        $collection = new ExploreResultCollection(
            $topicsResult,
            $queriesResult
        );

        $this->assertSame($queriesResult, $collection->getRelatedQueriesResult());
        $this->assertSame($topicsResult, $collection->getRelatedTopicsResult());
        $this->assertSame(
            [
                $topicsResult,
                $queriesResult
            ],
            $collection->getResults()
        );
    }

    public function testWillThrowExceptionIfNoRelatedQueriesResult(): void
    {
        $this->expectException(GoogleTrendsException::class);
        $this->expectExceptionMessage('No explore result available for related queries!');

        (new ExploreResultCollection(...[]))->getRelatedQueriesResult();
    }

    public function testWillThrowExceptionIfNoRelatedTopicsResult(): void
    {
        $this->expectException(GoogleTrendsException::class);
        $this->expectExceptionMessage('No explore result available for related topics!');

        (new ExploreResultCollection(...[]))->getRelatedTopicsResult();
    }

    public function testWillThrowExceptionIfNoInterestOverTimeResult(): void
    {
        $this->expectException(GoogleTrendsException::class);
        $this->expectExceptionMessage('No explore result available for interest over time!');

        (new ExploreResultCollection(...[]))->getInterestOverTimeResult();
    }
}
