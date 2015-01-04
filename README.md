Google Trends PHP API
=============

Easy way to request searchs on Google Trends and get a standard response in JSON or PHP DTO.

Samples
=============

```php
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
```

Installation
=============

1. Project available in https://packagist.org/packages/gabrielfs7/annotation to install via composer.
