<?php


class ItemCounter
{
   private static $link = null ;

   public static function count($url)
   {
      if (!$config = parse_ini_file('itemcounter.ini', TRUE)) throw new exception('Unable to open itemcounter.ini.');

      return $config['counter']['url']."?u=".$url;
   }

}



/**
Before:
**/

$words = array("Life" => 3, "is" => 7, "beautiful" => 19);
foreach($words as $word => $key){

  $url = "dictionary.php?w=$key";
  print "<a href='$url'>$word</a><br>";
}


/**
After:
**/

$words = array("Life" => 3, "is" => 7, "beautiful" => 19);
foreach($words as $word => $key){

  $url = ItemCounter::count("dictionary.php?w=$key");
  print "<a href='$url'>$word</a><br>";
}


//echo "<a href=".ItemCounter::count('image.jpg').">IMG</a>";

?>
