<?php require __DIR__ . '/../vendor/autoload.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Google Trends - Analysis</title>
</head>
<body>
<?php
try {
$results = (new Gsoares\GoogleTrends\Search())
    ->setCategory(Gsoares\GoogleTrends\Category::BEAUTY_AND_FITNESS)
    ->setLocation('US')
    ->setLanguage('en-US')
    ->addWord('hais')
    ->setLastDays(30)
    ->search();
?>
<h1>Google Trends - Results</h1>
<table border="1">
    <thead>
    <th>TOTAL</th>
    <th>URL</th>
    </thead>
    <tbody>
    <td><?=$results->totalResults ?></td>
    <td><a href="<?=$results->searchUrl ?>"><?=$results->searchUrl ?></a></td>
    </tbody>
</table>
<br/>
<br/>
<table border="1">
    <thead>
    <th>Term</th>
    <th>Ranking</th>
    <th>Search URL</th>
    <th>Search Image URL</th>
    <th>Trends URL</th>
    </thead>
    <tbody>
    <?php foreach ($results->results as $term) { ?>
    <td><?=$term->term ?></td>
    <td><?=$term->ranking ?>%</td>
    <td><a href="<?=$term->searchUrl ?>"><?php echo $term->searchUrl ?></a></td>
    <td><a href="<?=$term->searchImageUrl ?>"><?php echo $term->searchImageUrl ?></a></td>
    <td><a href="<?=$term->trendsUrl ?>"><?php echo $term->trendsUrl ?></a></td>
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