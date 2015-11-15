<?php

 if (!$config = parse_ini_file('itemcounter.ini', TRUE)) throw new exception('Unable to open itemcounter.ini.');

 
 $dsn = $config['database']['db_driver'];
 $dsn = $dsn.":host=".$config['database']['db_host'];
 $dsn = $dsn.((!empty($config['database']['db_port'])) ? (';port=' . $config['database']['db_port']) : '');
 $dsn = $dsn.';dbname='. $config['database']['db_name'];
    
 $conn = new PDO($dsn, $config['database']['db_user'], $config['database']['db_password']);
 $conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

  try{

    $stmt = "CREATE TABLE ".$config['counter']['db_table']." (key_url VARCHAR(80) PRIMARY KEY, count INT DEFAULT 0)";  
    $result = $conn->exec($stmt);
    
    echo "Installation successful!";
  }
  catch (PDOException $e) { 
    echo "Installation failed, cause: ".$e->getMessage();
  }
  catch (Exception $e) {
    echo "Installation failed, cause: ".$e;
  }
?>

