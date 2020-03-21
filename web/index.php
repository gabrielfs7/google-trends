<?php require __DIR__ . '/../vendor/autoload.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Google Trends - Analysis</title>
</head>
<body>
<?php
try {
    $results = (new GSoares\GoogleTrends\Search())
        ->setCategory(GSoares\GoogleTrends\Category::BEAUTY_AND_FITNESS)
        ->setLocation('US')
        ->setLanguage('en-US')
        ->addWord(isset($_GET['word']) ? $_GET['word'] : 'hair')
        ->setLastDays(90)
        ->searchRelatedTerms();
    ?>
    <h1>Google Trends - Results</h1>
    <table border="1">
        <thead>
        <th>TOTAL</th>
        <th colspan="2">URL</th>
        </thead>
        <tbody>
        <td><?= count($results->getResults()) ?></td>
        <td colspan="2"><a href="<?= $results->getSearchUrl() ?>"><?= $results->getSearchUrl() ?></a></td>
        </tbody>
        <thead>
        <th>Term</th>
        <th>Ranking</th>
        <th>Search URL</th>
        </thead>
        <tbody>
        <?php foreach ($results->getResults() as $term) { ?>
        <td><?= $term->getTerm() ?></td>
        <td><?= $term->getRanking() ?>%</td>
        <td><a href="<?= $term->getSearchUrl() ?>"><?php echo $term->getSearchUrl() ?></a></td>
        </tbody>
        <?php } ?>
    </table>
    <?php
} catch (\Exception $e) {
    echo '<h1>Google Trends - ERROR</h1>';
    echo $e->getMessage();
}
?>
</body>
</html>