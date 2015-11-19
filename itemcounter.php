<?php

class ItemCounter
{
   private static $link = null ;

   public static function count($url,$name)
   {
      $config = ItemCounter::loadconfig();
    
      if(substr($url,0,7)==="http://"){
         $target_url = $url;   
      }
      else{
         $target_url = "http://".$_SERVER['HTTP_HOST'];
         $pos = strpos($url,"/");
         if($pos===0)
         {
           $target_url=$target_url.$url;
         }
         else
         {
           $pos = strrpos($_SERVER['REQUEST_URI'],"/");
           if($pos===false) $target_url=$target_url."/".$url;
           else $target_url=$target_url.substr($_SERVER['REQUEST_URI'],0,$pos+1).$url;      
         }
         
         
      }

      return $config['counter']['url']."?n=".$name."&u=".$target_url;
   }


   public static function loadconfig()
   {
      if (!$config = parse_ini_file('itemcounter.ini', TRUE)) throw new exception('Unable to open configuration file "itemcounter.ini".'); 

      return $config;
   }

   public static function getConnection($dsn, $config){
      if(strpos($config['database']['db_driver'],"sqlite")===false)
        $conn = new PDO($dsn, $config['database']['db_user'], $config['database']['db_password']);
      else
        $conn = new PDO($dsn);
      $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
      return $conn;
   }

   public static function getdsn($config){
      $dsn = $config['database']['db_driver'];
      if(strpos($config['database']['db_driver'],"sqlite")===false){ 
        $dsn = $dsn.":host=".$config['database']['db_host'];
        $dsn = $dsn.((!empty($config['database']['db_port'])) ? (';port=' . $config['database']['db_port']) : '');
        $dsn = $dsn.';dbname='. $config['database']['db_name'];
      }
      return $dsn;
   }

   public static function dbquery()
   {
      $config = ItemCounter::loadconfig();
      $dsn = ItemCounter::getdsn($config);
      $conn = ItemCounter::getConnection($dsn,$config);

      $stmt = "SELECT * FROM ".$config['counter']['db_table'];  
      $result = $conn->query($stmt);

      $conn= null;
      return $result;
   }

   public static function dbinsert($keyurl, $item_name)
   {
      $config = ItemCounter::loadconfig();
      $dsn = ItemCounter::getdsn($config);
      $conn = ItemCounter::getConnection($dsn,$config);

      $user = "";
      if(isset($config['counter']['user_session_variable'])) 
      {
         session_start();
         $session_var_name = $config['counter']['user_session_variable'];
         $user = $_SESSION[$session_var_name];
      }
      
      $num = $conn->exec("UPDATE ".$config['counter']['db_table']." SET count = count+1 WHERE key_url = '".$keyurl."' AND user = '".$user."'");
      if($num==0)
      {
        $conn->exec("INSERT INTO ".$config['counter']['db_table']." VALUES('".$keyurl."','".$item_name."',1,'".$user."')");
      }
    
      $conn= null;
   }

   public static function createdb()
   {
      $config = ItemCounter::loadconfig();
      $dsn = ItemCounter::getdsn($config);
      $conn = ItemCounter::getConnection($dsn,$config);

      $stmt = "CREATE TABLE ".$config['counter']['db_table']." (key_url VARCHAR(80), name VARCHAR(255), count INT DEFAULT 0, user VARCHAR(80), PRIMARY KEY (key_url, user))";  
      $result = $conn->exec($stmt);

      $conn= null;
   }  

}
