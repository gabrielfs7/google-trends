<?php
require __DIR__ . '/../vendor/autoload.php';

use GSoares\GoogleTrends\Search\SearchFilter;
use GSoares\GoogleTrends\Search\RelatedQuerySearch;

header('Content-Type', 'application/json');

try {
    $relatedSearchUrlBuilder = (new SearchFilter())
        ->withCategory((int)($_GET['category'] ?? 0)) //All categories
        ->withSearchTerm($_GET['searchTerm'][0] ?? 'google')
        ->withLocation($_GET['location'] ?? 'US')
        ->withinInterval(
            new DateTimeImmutable($_GET['from'] ?? 'now -7 days'),
            new DateTimeImmutable($_GET['to'] ?? 'now')
        )
        ->withLanguage($_GET['language'] ?? 'en-US')
        ->considerWebSearch()
        # ->considerImageSearch() // Consider only image search
        # ->considerNewsSearch() // Consider only news search
        # ->considerYoutubeSearch() // Consider only youtube search
        # ->considerGoogleShoppingSearch() // Consider only Google Shopping search
        ->withTopMetrics()
        ->withRisingMetrics();

    echo json_encode(
        (new RelatedQuerySearch())
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