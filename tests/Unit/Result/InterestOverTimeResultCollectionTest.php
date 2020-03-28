<?php declare(strict_types=1);

namespace GSoares\GoogleTrends\Tests\Unit\Result;

use DateTimeImmutable;
use GSoares\GoogleTrends\Result\InterestOverTimeCollection;
use GSoares\GoogleTrends\Result\InterestOverTimeResult;
use PHPUnit\Framework\TestCase;

class InterestOverTimeResultCollectionTest extends TestCase
{
    public function testCanGetResults(): void
    {
        $result = new InterestOverTimeResult(new DateTimeImmutable(), [100], true);

        $collection = new InterestOverTimeCollection('url', $result);

        $this->assertSame('url', $collection->getSearchUrl());
        $this->assertSame(1, $collection->getTotalResults());
        $this->assertSame(
            [
                $result
            ],
            $collection->getResults()
        );
    }

    public function testJsonSerializeCollection(): void
    {
        $date = new DateTimeImmutable();

        $result = [
            'searchUrl' => 'url',
            'totalResults' => 1,
            'results' => [
                [
                    'interestAt' => $date->format(DATE_ATOM),
                    'values' => [
                        100
                    ],
                    'firstValue' => 100,
                    'hasData' => true,
                ]
            ],
        ];

        $this->assertSame(
            $result,
            json_decode(
                json_encode(
                    (new InterestOverTimeCollection(
                        'url',
                        new InterestOverTimeResult($date, [100], true)
                    ))->jsonSerialize()
                ),
                true
            )
        );
    }
}
