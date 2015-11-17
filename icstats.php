<?php

 if (!$config = parse_ini_file('itemcounter.ini', TRUE)) throw new exception('Unable to open itemcounter.ini.');

 
 $dsn = $config['database']['db_driver'];
 $dsn = $dsn.":host=".$config['database']['db_host'];
 $dsn = $dsn.((!empty($config['database']['db_port'])) ? (';port=' . $config['database']['db_port']) : '');
 $dsn = $dsn.';dbname='. $config['database']['db_name'];
    
 $conn = new PDO($dsn, $config['database']['db_user'], $config['database']['db_password']);
 $conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

  try{

    $stmt = "SELECT * FROM ".$config['counter']['db_table'];  
    $result = $conn->query($stmt);

    print "<table border='1'>";
    print "<tr><th>Key URL</th><th>Item name</th><th>Count</th><th>User</th></tr>";
    foreach($result as $row) {
       print "<tr><td>".$row['key_url']."</td><td>".$row['name']."</td><td>".$row['count']."</td></tr>";
    }
    print "</table>";

  }
  catch (PDOException $e) { 
    echo "Installation failed, cause: ".$e->getMessage();
  }
  catch (Exception $e) {
    echo "Installation failed, cause: ".$e;
  }
?>

