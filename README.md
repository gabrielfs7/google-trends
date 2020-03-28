# Google Trends PHP API

[![Total Downloads](https://img.shields.io/packagist/dt/gabrielfs7/google-trends?server=https%3A%2F%2Fpackagist.org)](https://packagist.org/packages/gabrielfs7/google-trends)
[![Latest Stable Version](https://img.shields.io/packagist/v/gabrielfs7/google-trends.svg?style=flat-square)](https://packagist.org/packages/gabrielfs7/google-trends)

![Branch master](https://img.shields.io/badge/branch-master-brightgreen.svg?style=flat-square)
[![Build Status](https://scrutinizer-ci.com/g/gabrielfs7/google-trends/badges/build.png?b=master)](https://scrutinizer-ci.com/g/gabrielfs7/google-trends/build-status/master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/gabrielfs7/google-trends/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/gabrielfs7/google-trends/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/gabrielfs7/google-trends/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/gabrielfs7/google-trends/?branch=master)

A easier way to search on Google Trends and get a standard response in JSON or PHP DTO.

## Dependencies

- PHP 7.2+
- PHP ext-json

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

Only a few steps!

### 1) Create a `SearchFilter` with your restrictions

```php
use GSoares\GoogleTrends\Search\SearchFilter;

$searchFilter = (new SearchFilter())
        ->withCategory(0) //All categories
        ->withSearchTerm('google')
        ->withLocation('US')
        ->withinInterval(
            new DateTimeImmutable('now -7 days'),
            new DateTimeImmutable('now')
        )
        ->withLanguage('en-US')
        ->considerWebSearch()
        # ->considerImageSearch() // Consider only image search
        # ->considerNewsSearch() // Consider only news search
        # ->considerYoutubeSearch() // Consider only youtube search
        # ->considerGoogleShoppingSearch() // Consider only Google Shopping search
        ->withTopMetrics()
        ->withRisingMetrics();
```

### 2) Execute the search you wish to

```php
use GSoares\GoogleTrends\Search\RelatedTopicsSearch;
use GSoares\GoogleTrends\Search\RelatedQueriesSearch;

// Get related query results
$result = (new RelatedQueriesSearch())
    ->search($searchFilter)
    ->jsonSerialize();

// Get related topics results
$result = (new RelatedTopicsSearch())
    ->search($searchFilter)
    ->jsonSerialize();
```

JSON response example:

```json
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
      }
   ]
}
```

## Installation

The Project is available on [Packagist](https://packagist.org/packages/gabrielfs7/google-trends) and you can install it using [Composer](http://getcomposer.org/):

```shell script
composer install gabrielfs7/google-trends
```

## Example

After install it you can access an working example [here](/web/index.php).

## Google Categories

You can find the categories available on Google [here](/misc/categories.json).

## Contributing

I am really happy you can help with this project. If you are interest on how to contribute, please check [this page](./CONTRIBUTING.md).