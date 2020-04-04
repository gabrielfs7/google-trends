<?php declare(strict_types=1);

namespace GSoares\GoogleTrends\Tests\Unit\Result;

use GSoares\GoogleTrends\Result\RelatedResult;
use GSoares\GoogleTrends\Result\RelatedResultCollection;
use PHPUnit\Framework\TestCase;

class RelatedResultCollectionTest extends TestCase
{
    public function testCanGetResults(): void
    {
        $result = new RelatedResult('term', true, 100, 'link');

        $collection = new RelatedResultCollection('url', $result);

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
        $result = [
            'searchUrl' => 'url',
            'totalResults' => 2,
            'results' => [
                [
                    'term' => 'term',
                    'hasData' => true,
                    'ranking' => 100,
                    'value' => 100,
                    'searchUrl' => 'link',
                    'metricType' => 'TOP',
                ],
                [
                    'term' => 'term2',
                    'hasData' => false,
                    'ranking' => 99,
                    'value' => 99,
                    'searchUrl' => 'link2',
                    'metricType' => 'RISING',
                ]
            ],
        ];

        $this->assertSame(
            $result,
            json_decode(
                json_encode(
                    (new RelatedResultCollection(
                        'url',
                        ...[
                            new RelatedResult('term', true, 100, 'link', 'TOP'),
                            new RelatedResult('term2', false, 99, 'link2', 'RISING'),
                        ]
                    ))->jsonSerialize()
                ),
                true
            )
        );
    }
}
