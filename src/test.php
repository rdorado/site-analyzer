<?php include 'SiteAnalyzer.php'; ?>
<?php

$r1 = rand(1,3);
print("<a href='test.php?id=$r1'>Ref</a>");

SiteAnalyzer::count();
$data = SiteAnalyzer::getStats();
print( SiteAnalyzer::transform($data, "html") );
	
//SiteAnalyzer::incoming(id1);

//SiteAnalyzer::report();

//SiteAnalyzer::abtest(id1,id2);


