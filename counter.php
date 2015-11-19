<?php include("itemcounter.php"); ?>
<?php

 $keyurl = $_GET['u'];
 $name = $_GET['n'];   

 ItemCounter::dbinsert($keyurl, $name);
 header("Location: ".$_GET['u']);

 die();

?>
