<?php 
require_once __DIR__ . '/vendor/autoload.php';


//include_once 'SiteAnalyzer.php';
use SiteAnalyzer\SiteAnalyzer;

$r1 = rand(1,3);
$r2 = rand(1,10);
print("<a href='example.php?id=$r1'>Ref</a>");
SiteAnalyzer::count(["user"=>$r2]);

print("<h1>Basic Analytics</h1>");
print("<h2>Example 1: basic info</h2>");
$data = SiteAnalyzer::getStats();
print( SiteAnalyzer::transform($data, "html") );

print("<h2>Example 2: time analysis</h2>");	
$data = SiteAnalyzer::groupHitsByTime(["daily"]);
print( SiteAnalyzer::transform($data, "html") );

print("<h2>Example 3: user analysis</h2>");	
$data = SiteAnalyzer::groupHitsByUser();
print( SiteAnalyzer::transform($data, "html") );

/*
print("<h1>Path Analytics</h1>");
print("<h2>Example 4: math matrix</h2>");
$data = SiteAnalyzer::getStats();
print( SiteAnalyzer::transform($data, "html") );
print("<h2>Example 5: a/b test</h2>");
$data = SiteAnalyzer::getStats();
print( SiteAnalyzer::transform($data, "html") );


print("<h1>Profile Analyisis</h1>");
print("<h2>Example 6: User profile</h2>");
$data = SiteAnalyzer::getStats();
print( SiteAnalyzer::transform($data, "html") );
print("<h2>Example 7: Time profile</h2>");
$data = SiteAnalyzer::getStats();
print( SiteAnalyzer::transform($data, "html") );
print("<h2>Example 8: Page profile</h2>");
$data = SiteAnalyzer::getStats();
print( SiteAnalyzer::transform($data, "html") );
*/
