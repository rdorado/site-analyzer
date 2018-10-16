<?php
/**
 *
 * (c) Ruben Dorado <ruben.dorados@google.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
include_once 'exceptions.php';

/**
 * class SiteAnalyzer
 *
 * @package   SiteAnalyzer
 * @author    Ruben Dorado <ruben.dorados@gmail.com>
 * @copyright 2018 Ruben Dorado
 * @license   http://www.opensource.org/licenses/MIT The MIT License
 */
class Persistence{

    /*
     * @param Configuration $config
     *
     * @return PDO
     */
    public static function getPDO($config){
        try{
            $options = array(
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            );
            return new PDO($config->getDsn(),$config->getUser(),$config->getPassword(),$options);
         }
         catch(Exception $e){
            throw new PersistenceException("Could not create pdo connection.");
         }
    }


    /*
     * @param $pdo PDO
     * @param $config Configuration
     *
     */
    public static function crateDatabase($pdo, $config){
        try{

            $db_main_table = $config->getMainTableName();
            $db_options_table = $config->getOptionsTableName();           
            $db_from_table = $config->getFromTableName();

            $stmt = $pdo->prepare("CREATE TABLE $db_main_table (id VARCHAR(255), url VARCHAR(255), count INT)");
            $stmt->execute();
            $stmt = $pdo->prepare("CREATE TABLE $db_options_table (id VARCHAR(255), time TIMESTAMP, user VARCHAR(255))");
            $stmt->execute();
            $stmt = $pdo->prepare("CREATE TABLE $db_from_table (id VARCHAR(255), from_id VARCHAR(255), count INT)");
            $stmt->execute();
        }
        catch(Exception $e){
            throw new DatabaseException("Could not create the database. ".$e->getMessage());
        }        
        return true;

    }


    /*
     * @param $pdo PDO
     * @param $config Configuration
     *
     */
    public static function deleteDatabase($pdo, $config){
        $resp = true;
        
        $db_main_table = $config->getMainTableName();
        $db_options_table = $config->getOptionsTableName();
        $db_from_table = $config->getFromTableName();
        
        $resp = $resp && Persistence::dropTable($pdo, $db_main_table);
        $resp = $resp && Persistence::dropTable($pdo, $db_options_table);
        $resp = $resp && Persistence::dropTable($pdo, $db_from_table);
        
        return $resp;
    }


    /*
     * @param $pdo PDO
     * @param $config Configuration
     *
     */
    private static function dropTable($pdo, $tableName){
        try{
            
            $stmt = $pdo->prepare("DROP TABLE $tableName");
            $stmt->execute();
           
        }
        catch(Exception $e){
            throw new DatabaseException("Problem deleting the table $tableName. ".$e->getMessage());
        }
        return true;
    }
    

    /*
     * @param $pdo PDO
     * @param $config Configuration
     *
     */
    public static function checkTables($pdo, $config){
        $resp = true;      
        try{
            
            $resp = $resp && Persistence::checkMainTable($pdo, $config);
            $resp = $resp && Persistence::checkOptionsTable($pdo, $config);
            $resp = $resp && Persistence::checkFromTable($pdo, $config);
            
        }
        catch(Exception $e){
            return false;
        }        
        return $resp;

    }

    /*
     * @param $pdo PDO
     * @param $config Configuration
     */
    public static function checkFromTable($pdo, $config){
        try{
            $db_from_table = $config->getFromTableName();
            $stmt = $pdo->prepare("SELECT * FROM $db_from_table WHERE 1==0");
            $stmt->execute();
            
        }
        catch(Exception $e){
            return false;
        }
        return true;
    }
        
    /*
     * @param $pdo PDO
     * @param $config Configuration
     */
    public static function checkOptionsTable($pdo, $config){
        try{
            $db_options_table = $config->getOptionsTableName();
            $stmt = $pdo->prepare("SELECT * FROM $db_options_table WHERE 1==0");
            $stmt->execute();
        }
        catch(Exception $e){
            return false;
        }
        return true;
    }
    
    /*
     * @param $pdo PDO
     * @param $config Configuration
     */
    public static function checkMainTable($pdo, $config){
        try{
            $db_main_table = $config->getMainTableName();
            $stmt = $pdo->prepare("SELECT * FROM $db_main_table WHERE 1==0");
            $stmt->execute();            
        }
        catch(Exception $e){
            return false;
        }
        return true;        
    }
        
    /*
     * @param $pdo PDO
     * @param $config Configuration
     *
     */
    public static function updateCount($pdo, $config, $options=[]){

        try{
        
            $db_main_table = $config->getMainTableName();
            $db_options_table = $config->getOptionsTableName();
            $db_from_table = $config->getFromTableName();            

            $store_from = true;
            $store_time = true;
            $store_user = true;
            
            if(array_key_exists('url', $options)){
                $url = $options['url'];
            }
            else if(array_key_exists('HTTP_HOST',$_SERVER)){
                $url = "http://".$_SERVER['HTTP_HOST'];
                if(array_key_exists('REQUEST_URI',$_SERVER)){
                    $url=$url.$_SERVER['REQUEST_URI'];
                }
                
            }
            else{
                $url = "No Info";
            }

            if($config->getRemoveQueryString()){
                $url = preg_replace('/\?.*/', '', $url);
            }
            
            if(array_key_exists('id', $options)){
                $id = $options['id'];
            }
            else{
                $id = $url;
            }

            $stmt = $pdo->prepare("UPDATE $db_main_table SET count = count + 1 WHERE id = ?");
            $stmt->execute([$id]);
            if( $stmt->rowCount() == 0 ){
                $stmt = $pdo->prepare("INSERT INTO $db_main_table (id, url, count) VALUES (?, ?, 1)");
                $stmt->execute([$id, $url]);
            }

            if($store_from){
                if(array_key_exists('from_id', $options)){
                    $ids = [$options['from_id']];
                }
                else{
                    $from_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
                    $ids = Persistence::findHitsByUrl($from_url);
                    
                }
                 
                foreach ($ids as $from_id) {
                    $stmt = $pdo->prepare("UPDATE $db_from_table SET count = count + 1 WHERE id = ? and from_id = ?");
                    $stmt->execute([$id, $from_id]);
                
                    if( $stmt->rowCount() == 0 ){
                        $stmt = $pdo->prepare("INSERT INTO $db_from_table (id, from_id, count) VALUES (?, ?, 1)");
                        $stmt->execute([$id, $from_id]);
                    }
                }
            }
                        
            $user = null;
            if($store_user){
                if(array_key_exists('user', $options)){
                    $user = $options['user'];
                }    
            }
            
            if($store_time || $store_user){
                $stmt = $pdo->prepare("INSERT INTO $db_options_table (id, time, user) VALUES (?, ?, ?)");
                $stmt->execute([$id, time(), $user]);
            }
                        
            $stmt = null;
        }
        catch(Exception $e){
            throw new DatabaseException("Could not update the count.".$e->getMessage());
        }        
    }

    /*
     * @param $pdo PDO
     * @param $config Configuration
     *
     */
    public static function findHitsByUrl($pdo, $config, $url){
        $resp = [];
        try{
            
            $dbtable = $config->getMainTableName();
            $stmt = $pdo->prepare("SELECT id,url,count FROM $dbtable WHERE url = '$url'");
            if($stmt->execute()){
                while($row = $stmt->fetch()){
                    $resp[] = [$row['id'],$row['url'],$row['count']];
                }
            }
        }
        catch(Exception $e){
            throw new DatabaseException("Error executing function 'findHitsByUrl'. ".$e->getMessage());
        }
        return $resp;
        
    }
    
    /*
     * @param $pdo PDO
     * @param $config Configuration
     *
     */
    public static function getCounts($pdo, $config)
    {
        $resp = [];
        try{

            $dbtable = $config->getCountTableName();
            $stmt = $pdo->prepare("SELECT * FROM $dbtable group by id");
            if($stmt->execute()){
                while($row = $stmt->fetch()){
                    $tmp = [];
                    for($i=0;$i<6;$i++){
                        $tmp[] = $row[$i];
                    }
                    $resp[] = $tmp;
                }
            }
            
            $stmt = null;
        }
        catch(Exception $e){
            throw new DatabaseException("Could not update the count.");
        }        
        return $resp;
    }

    public static function getCountsById($pdo, $config){
        return "Works";
    }
}


