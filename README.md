# Google Trends PHP API

Easy way to request search on Google Trends and get a standard response in JSON or PHP DTO.

## Advantages on using this API

- Get standard response that can be easily imported to your BI system.
- No need to have a google account.
- No need for web scraping data from Google Trends UI.
- We deal with Google request token handling for you.
- Allows you to create custom reports that better fit to your business. 

## Current support

- Related topics Search.
- Related queries Search.
- Search by categories.
- Search by location.
- Language support.
- Includes top or rising metrics.
- Search type:
  - Web
  - Image
  - News
  - Youtube
  - Google Shopping

### TODO

- Add support for "time series" results.
- Add support for "interests by region" results.

## Usage

Only 3 simple steps!

1) Create a `SearchFilter` with your restrictions.
2) Chose the type of search you want to do.
3) Execute the search and get the results!

```php
<?php
use GSoares\GoogleTrends\Search\RelatedTopicsSearch;
use GSoares\GoogleTrends\Search\SearchFilter;
use GSoares\GoogleTrends\Search\RelatedQueriesSearch;

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

    // Get related query results
    $result = (new RelatedQueriesSearch())
        ->search($relatedSearchUrlBuilder)
        ->jsonSerialize();

    // Get related topics results
    $result = (new RelatedTopicsSearch())
        ->search($relatedSearchUrlBuilder)
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

## Example

You can access an working example [here](/web/index.php).

## Google Categories

You can find the categories available on Google [here](/config/categories.json).