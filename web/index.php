<?php
require __DIR__ . '/../vendor/autoload.php';

use GSoares\GoogleTrends\Builder\RelatedSearchUrlBuilder;
use GSoares\GoogleTrends\Search\RelatedSearch;

header('Content-Type', 'application/json');

try {
    $relatedSearchUrlBuilder = (new RelatedSearchUrlBuilder())
        ->withToken($_GET['token'] ?? '')
        ->withCategory((int)($_GET['category'] ?? 44)) //Beauty & Fitness
        ->withWord($_GET['word'] ?? 'hair')
        ->withinLastDays(365)
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