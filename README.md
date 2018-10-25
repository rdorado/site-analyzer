# Site Analyzer

Site Dynamics Analyzer is php-based tool to analyse the dynamics of a web site.

## Installation

Add the following dependency to composer

```
sa
```

## Basic usage

Basic example: 
```php 
use SiteAnalyzer;

SiteAnalyzer::count("Test"); 
```
Displaying the current counts:

```php 
$data = SiteAnalyzer::getStats();
print( SiteAnalyzer::transform($data, "html") );
``` 

## Other features:

### Example 2: time analysis
$data = SiteAnalyzer::getStats();
print( SiteAnalyzer::transform($data, "html") );

### Example 3: user analysis
$data = SiteAnalyzer::getStats();
print( SiteAnalyzer::transform($data, "html") );


### Path Analytics

###  Example 4: math matrix
$data = SiteAnalyzer::getStats();
print( SiteAnalyzer::transform($data, "html") );

### Example 5: a/b test</h2>");
$data = SiteAnalyzer::getStats();
print( SiteAnalyzer::transform($data, "html") );

### Profile Analyisis
### Example 6: User profile
$data = SiteAnalyzer::getStats();
print( SiteAnalyzer::transform($data, "html") );
### Example 7: User-Time profile
$data = SiteAnalyzer::getStats();
print( SiteAnalyzer::transform($data, "html") );

### Page profiling
$data = SiteAnalyzer::getStats();
print( SiteAnalyzer::transform($data, "html") );
