<?php
require __DIR__ . '/../vendor/autoload.php';

use GSoares\GoogleTrends\Search;
use GSoares\GoogleTrends\Category;

header('Content-Type', 'application/json');

try {
    $search = new Search();
    $search->getQueryBuilder()
        ->withToken($_GET['token'] ?? '')
        ->withCategory($_GET['category'] ?? 44) //Beauty & Fitness
        ->withWord($_GET['word'] ?? 'hair')
        ->withLastDays(90)
        ->withTopMetrics()
        ->withRisingMetrics();

    echo json_encode($search->searchRelatedTerms()->jsonSerialize());
} catch (Throwable $exception) {
    echo json_encode(
        [
            'exception' => get_class($exception),
            'error' => $exception->getMessage()
        ]
    );
}