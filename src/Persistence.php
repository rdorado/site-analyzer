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
            $stmt = $pdo->prepare("CREATE TABLE $db_options_table (id VARCHAR(255), time TIMESTAMP, user )");
            $stmt = $pdo->prepare("CREATE TABLE $db_from_table (id1 VARCHAR(255), id2 VARCHAR(255))");

        }
        catch(Exception $e){
            throw new DatabaseException("Could not update the count.");
        }        
        return $resp;

    }


    /*
     * @param $pdo PDO
     * @param $config Configuration
     *
     */
    public static function deleteDatabase($pdo, $config){
        try{
            $db_main_table = $config->getMainTableName();
            $db_options_table = $config->getOptionsTableName();           
            $db_from_table = $config->getFromTableName();

            $stmt = $pdo->prepare("DROP TABLE $db_main_table");
            $stmt->execute();
            
            $stmt = $pdo->prepare("DROP TABLE $db_options_table");
            $stmt->execute();
            
            $stmt = $pdo->prepare("DROP TABLE $db_from_table");
            $stmt->execute();
        }
        catch(Exception $e){
            throw new DatabaseException("Problem deleting the tables. ".$e->getMessage());
        }        
        return true;
    }



    /*
     * @param $pdo PDO
     * @param $config Configuration
     *
     */
    public static function checkTables($pdo, $config){
        try{

            $db_main_table = $config->getMainTableName();
            $db_options_table = $config->getOptionsTableName();           
            $db_from_table = $config->getFromTableName();

            $stmt = $pdo->prepare("SELECT id,url,count FROM $db_main_table WHERE 1 != 0");
            $stmt->execute();
            
            $stmt = $pdo->prepare("SELECT id,time,user FROM $db_options_table WHERE 1 != 0");
            $stmt->execute();
            
            $stmt = $pdo->prepare("SELECT id,from,count FROM $db_from_table WHERE 1 != 0");
            $stmt->execute();

        }
        catch(Exception $e){
            throw new DatabaseException("Could not update the count.");
        }        
        return true;

    }


    /*
     * @param $pdo PDO
     * @param $config Configuration
     *
     */
    public static function updateCount($pdo, $config){

        try{

            $id = '';
            $url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            $from_id = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
            $id = $url;
            $time = $config->getStoreTime() ? time() : 0;
            $user = '';

            $dbtable = $config->getCountTableName();

            $stmt = $pdo->prepare("UPDATE $dbtable SET count = count + 1 WHERE id = ? AND url = ? AND from_id = ? AND time = ? AND user = ?");
            $stmt->execute([$id, $url, $from_id, $time, $user]);
            if( $stmt->rowCount() == 0 ){
                $stmt = $pdo->prepare("INSERT INTO $dbtable (id, url, from_id, time, user, count) VALUES (?, ?, ?, ?, ?, 1)");
                $stmt->execute([$id, $url, $from_id, $time, $user]);
            }
            $stmt = null;
        }
        catch(Exception $e){
            throw new DatabaseException("Could not update the count.");
        }        
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


