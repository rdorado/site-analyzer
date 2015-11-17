<?php


 if (!$config = parse_ini_file('itemcounter.ini', TRUE)) throw new exception('Unable to open itemcounter.ini.');

 $dsn = $config['database']['db_driver'];
 $dsn = $dsn.":host=".$config['database']['db_host'];
 $dsn = $dsn.((!empty($config['database']['db_port'])) ? (';port=' . $config['database']['db_port']) : '');
 $dsn = $dsn.';dbname=' . $config['database']['db_name'];
    
 $conn = new PDO($dsn, $config['database']['db_user'], $config['database']['db_password']);
 $conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );  
  
 try{

   session_start();
   $session_var_name = $config['counter']['db_table'];
   $user = $_SESSION[$session_var_name];
   
   $num = $conn->exec("UPDATE ".$config['counter']['db_table']." SET count = count+1 WHERE key_url = '".$_GET['u']."' AND user = '".$user."'");
   if($num==0){
      $item_name = $_GET['n'];   
      $conn->exec("INSERT INTO ".$config['counter']['db_table']." VALUES('".$_GET['u']."','".$item_name."',1,'".$user."')");
   }
 } 
 catch(PDOException $e){
    echo "Error inserting, cause: ".$e->getMessage();
 }

 header("Location: ".$_GET['u']);
 die();
?>
