<?php
require __DIR__ . '/../vendor/autoload.php';

use GSoares\GoogleTrends\Builder\RelatedSearchUrlBuilder;
use GSoares\GoogleTrends\Search\RelatedSearch;

header('Content-Type', 'application/json');

try {
    $relatedSearchUrlBuilder = (new RelatedSearchUrlBuilder())
        ->withCategory((int)($_GET['category'] ?? 0)) //All categories
        ->withSearchTerm($_GET['searchTerm'][0] ?? 'google')
        ->withLocation($_GET['location'] ?? 'US')
        ->withinInterval(
            new DateTimeImmutable($_GET['from'] ?? 'now -7 days'),
            new DateTimeImmutable($_GET['to'] ?? 'now')
        )
        ->withLanguage($_GET['language'] ?? 'en-US')
        ->withTopMetrics()
        ->withRisingMetrics();

    echo json_encode(
        (new RelatedSearch())
            ->search($relatedSearchUrlBuilder)
            ->jsonSerialize()
    );
} catch (Throwable $exception) {
    echo json_encode(
        [
            'exception' => get_class($exception),
            'error' => $exception->getMessage()
        ]
    );
}