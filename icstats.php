<?php include("itemcounter.php"); ?>
<html>
 <head>
  <style>
body { text-align: center; font: 11px verdana, arial, helvetica, sans-serif; border:0; padding: 0; margin: 0; height:100%; width:100%;}
h1 { text-align: center;}
.main { margin: 0; border: 1px solid #EEEEEE; margin-left:10%; margin-right:10%; background-color: #F9F9F9; height:100%; width:80%; }
table { margin-left:10%; width:80%; border-collapse: collapse; }
th		{ background-color:#EEEEEE; border-color: #ECECEC; border-left-width: 0px; border-right-width: 1px; border-top-width: 0px; border-bottom-width: 1px; padding: 1px 2px 1px 1px; font: 11px verdana, arial, helvetica, sans-serif; text-align:center; color: #000000; }
td	{ background-color: #FFFFFF; border-color: #ECECEC; border-left-width: 0px; border-right-width: 1px; border-top-width: 0px; border-bottom-width: 1px; font: 11px verdana, arial, helvetica, sans-serif; text-align:center; color: #000000; }
  </style>
 </head>
 <body>
  <div class="main">
   <h1>Item counter statistics</h1>
<?php

 $config = ItemCounter::loadconfig();

  try{
    $result = ItemCounter::dbquery();

    $show_user = isset($config['counter']['user_session_variable']) ? true : false;
    print "<table border='1'>";
    print "<tr><th>Key URL</th><th>Item name</th><th>Count</th>";
    if($show_user) print "<th>User</th>";
    print "</tr>";
    foreach($result as $row) {
       print "<tr><td>".$row['key_url']."</td><td>".$row['name']."</td><td>".$row['count']."</td>";
       if($show_user) print "<td>".$row['user']."</td>";
       print "</tr>";
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
  </div>
 </body>
</html>
