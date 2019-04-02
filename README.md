Site Analyzer
=============

[![MIT License](https://badgen.net/badge/license/MIT/)](http://opensource.org/licenses/MIT)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/rdorado/site-analyzer/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/rdorado/site-analyzer/)
[![Build Status](https://travis-ci.com/rdorado/site-analyzer.svg?branch=master)](https://travis-ci.com/rdorado/site-analyzer)

SiteAnalyzer is a free package for analyzing PHP sites. With SiteAnalyzer, it is possible to count pages, understand user behavior, analyze site dynamic, and perform A/B testings. SiteAnalyzer can be installed into any PHP in seconds without affecting the bussiness logic of the application and customized according to the needs. 

Features
--------


- Click counter
- Site dynamics analysis
- User profile analysis
- A/B testing


Installation
------------

SiteAnalyzer can be installed through [Composer](https://getcomposer.org):

```shell
$ composer require rdorado/site-analyzer
```


Or modify your composer.json requirements:

```json
    "require": {
        "rdorado/site-analyzer": "^0.1.1"
    }
```
and update your project:

```shell
$ composer update
```

Usage
-----

Count all the views/pages you want to observe by importing the ```SiteAnalyzer``` class and then using the ```count``` static method:

```php
use SiteAnalyzer\SiteAnalyzer;

SiteAnalyzer::count();
```

You can also include other options to be stored on the database. That depends on the kind of reportings or analyzis you want to perform:

```php
$options = ['id' = $myid];
SiteAnalyzer::count($options);
```


Reporting/Displaying the stored information
===========================================

Once you have started to count the page hits, different sort of reports can be displayed. For example, a very basic report is the number of hits per page stored on the database:

```php 
$data = SiteAnalyzer::getStats();
print( SiteAnalyzer::transform($data, "html") );
``` 

Documentation
===========================================

Read the docs at https://site-analyzer.readthedocs.io/en/latest/.



