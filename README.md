Google Trends PHP API
=============

Easy way to request searchs on Google Trends and get a standard response in JSON or PHP DTO.

Samples
=============

```php
<?php
$results = (new GSoares\GoogleTrends\Search())
    ->setCategory(GSoares\GoogleTrends\Category::BEAUTY_AND_FITNESS)
    ->setLocation('US')
    ->setLanguage('en-US')
    ->addWord('hair')
    ->setLastDays(30) // allowed: 7, 30, 90, 365
    ->searchJson();
    
/* $results = (string) 
{  
   "searchUrl":"http://www.google.com/trends/fetchComponent?hl=en-US&cat=0-44&geo=US&q=hair&cid=TOP_QUERIES_0_0&date=today+30-d&cmpt=q&content=1&export=3",
   "totalResults":10,
   "results":[  
      {  
         "term":"hair salon",
         "ranking":100,
         "searchUrl":"http://www.google.com/search?q=%22hair+salon%22",
         "searchImageUrl":"http://www.google.com/search?q=%22hair+salon%22&tbm=isch",
         "trendsUrl":"http://www.google.com/trends/explore#cat=0-44&geo=US&q=%22hair+salon%22&cmpt=q&date=today+30-d"
      },
      {  
         "term":"short hair",
         "ranking":85,
         "searchUrl":"http://www.google.com/search?q=%22short+hair%22",
         "searchImageUrl":"http://www.google.com/search?q=%22short+hair%22&tbm=isch",
         "trendsUrl":"http://www.google.com/trends/explore#cat=0-44&geo=US&q=%22short+hair%22&cmpt=q&date=today+30-d"
      },
      {  
         "term":"hair color",
         "ranking":85,
         "searchUrl":"http://www.google.com/search?q=%22hair+color%22",
         "searchImageUrl":"http://www.google.com/search?q=%22hair+color%22&tbm=isch",
         "trendsUrl":"http://www.google.com/trends/explore#cat=0-44&geo=US&q=%22hair+color%22&cmpt=q&date=today+30-d"
      },
      {  
         "term":"long hair",
         "ranking":80,
         "searchUrl":"http://www.google.com/search?q=%22long+hair%22",
         "searchImageUrl":"http://www.google.com/search?q=%22long+hair%22&tbm=isch",
         "trendsUrl":"http://www.google.com/trends/explore#cat=0-44&geo=US&q=%22long+hair%22&cmpt=q&date=today+30-d"
      },
      {  
         "term":"hairstyles",
         "ranking":75,
         "searchUrl":"http://www.google.com/search?q=%22hairstyles%22",
         "searchImageUrl":"http://www.google.com/search?q=%22hairstyles%22&tbm=isch",
         "trendsUrl":"http://www.google.com/trends/explore#cat=0-44&geo=US&q=%22hairstyles%22&cmpt=q&date=today+30-d"
      },
      {  
         "term":"black hair",
         "ranking":70,
         "searchUrl":"http://www.google.com/search?q=%22black+hair%22",
         "searchImageUrl":"http://www.google.com/search?q=%22black+hair%22&tbm=isch",
         "trendsUrl":"http://www.google.com/trends/explore#cat=0-44&geo=US&q=%22black+hair%22&cmpt=q&date=today+30-d"
      },
      {  
         "term":"natural hair",
         "ranking":60,
         "searchUrl":"http://www.google.com/search?q=%22natural+hair%22",
         "searchImageUrl":"http://www.google.com/search?q=%22natural+hair%22&tbm=isch",
         "trendsUrl":"http://www.google.com/trends/explore#cat=0-44&geo=US&q=%22natural+hair%22&cmpt=q&date=today+30-d"
      },
      {  
         "term":"blonde hair",
         "ranking":55,
         "searchUrl":"http://www.google.com/search?q=%22blonde+hair%22",
         "searchImageUrl":"http://www.google.com/search?q=%22blonde+hair%22&tbm=isch",
         "trendsUrl":"http://www.google.com/trends/explore#cat=0-44&geo=US&q=%22blonde+hair%22&cmpt=q&date=today+30-d"
      },
      {  
         "term":"hair styles",
         "ranking":50,
         "searchUrl":"http://www.google.com/search?q=%22hair+styles%22",
         "searchImageUrl":"http://www.google.com/search?q=%22hair+styles%22&tbm=isch",
         "trendsUrl":"http://www.google.com/trends/explore#cat=0-44&geo=US&q=%22hair+styles%22&cmpt=q&date=today+30-d"
      },
      {  
         "term":"curly hair",
         "ranking":50,
         "searchUrl":"http://www.google.com/search?q=%22curly+hair%22",
         "searchImageUrl":"http://www.google.com/search?q=%22curly+hair%22&tbm=isch",
         "trendsUrl":"http://www.google.com/trends/explore#cat=0-44&geo=US&q=%22curly+hair%22&cmpt=q&date=today+30-d"
      }
   ]
}
*/
    
?>
```

Installation
=============

1. Project available in https://packagist.org/packages/gabrielfs7/google-trends to install via composer.
