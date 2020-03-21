# Google Trends PHP API

Easy way to request search on Google Trends and get a standard response in JSON or PHP DTO.

- Currently supports only related search terms

## Usage

```php
<?php
$results = (new GSoares\GoogleTrends\Search())
    ->setCategory(GSoares\GoogleTrends\Category::BEAUTY_AND_FITNESS)
    ->setLocation('US')
    ->setLanguage('en-US')
    ->addWord('hair')
    ->setLastDays(30) // allowed: 7, 30, 90, 365
    ->searchRelatedTerms()
    ->jsonSerialize();
    
/* $results = (string) 
{  
   "searchUrl":"http://www.google.com/trends/...",
   "totalResults":10,
   "results":[  
      {  
         "term":"hair salon",
         "ranking":100,
         "hasData": true,
         "searchUrl":"http://trends.google.com/..."
      },
      {  
         "term":"short hair",
         "ranking":85,
         "hasData": true,
         "searchUrl":"http://trends.google.com/..."
      },
      {...}
   ]
}
*/

?>
```

## Installation

1. Project available in https://packagist.org/packages/gabrielfs7/google-trends to install via composer.
