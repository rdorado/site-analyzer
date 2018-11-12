<?php
/**
 *
 * (c) Ruben Dorado <ruben.dorados@google.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SiteAnalyzer;

use Exception;
use PDO;

/**
 * class Persistence
 *
 * @package   SiteAnalyzer
 * @author    Ruben Dorado <ruben.dorados@gmail.com>
 * @copyright 2018 Ruben Dorado
 * @license   http://www.opensource.org/licenses/MIT The MIT License
 */
class Persistence
{

    /*
     * @param Configuration $config
     *
     * @return PDO
     */
    public static function getPDO($config) {
        $options = array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        );
        
        if ($config->getDsn()) {
            try {
                return new PDO($config->getDsn(), $config->getUser(), $config->getPassword(), $options);
            } catch (Exception $e) {                
                if (!$config->getUseOnMemoryDB()) {
                    throw new Exception("Could not create a db connection. Check permissions, configuration, and documentation. ".$e->getMessage());
                }
            }
        }
        
        if ($config->getUseOnMemoryDB()) {
            try {
                return new PDO("sqlite::memory:", null, null, $options);
            } catch (Exception $e) {
                throw new Exception("Could not create a db connection. Check permissions, configuration, and documentation. ".$e->getMessage());                
            }
        }
        throw new Exception("Error when trying to obtain a connection to a database. Check the configuration. ");

    }


    /*
     * @param $pdo PDO
     * @param $config Configuration
     *
     */
    public static function crateDatabase($pdo, $config) {
        try {
            $db_hit_table = $config->getHitTableName();
            $db_options_table = $config->getOptionsTableName();           
            $db_from_table = $config->getFromTableName();
            $db_url_table = $config->getUrlTableName();

            $stmt = $pdo->prepare("CREATE TABLE $db_hit_table (id VARCHAR(255), count INT)");
            $stmt->execute();
            $stmt = $pdo->prepare("CREATE TABLE $db_options_table (id VARCHAR(255), time TIMESTAMP, user VARCHAR(255))");
            $stmt->execute();
            $stmt = $pdo->prepare("CREATE TABLE $db_from_table (id VARCHAR(255), from_id VARCHAR(255), count INT)");
            $stmt->execute();
            $stmt = $pdo->prepare("CREATE TABLE $db_url_table (id VARCHAR(255), url VARCHAR(255), count INT)");
            $stmt->execute();
        } catch (Exception $e) {
            throw new Exception("Could not create the database. ".$e->getMessage());
        }        
        return true;
    }


    /*
     * @param $pdo PDO
     * @param $config Configuration
     *
     */
    public static function deleteDatabase($pdo, $config) {
        $resp = true;
        
        $db_hit_table = $config->getHitTableName();
        $db_options_table = $config->getOptionsTableName();
        $db_from_table = $config->getFromTableName();
        $db_url_table = $config->getUrlTableName();
        
        $resp = $resp && Persistence::dropTable($pdo, $db_hit_table);
        $resp = $resp && Persistence::dropTable($pdo, $db_options_table);
        $resp = $resp && Persistence::dropTable($pdo, $db_from_table);
        $resp = $resp && Persistence::dropTable($pdo, $db_url_table);
        
        return $resp;
    }


    /*
     * @param $pdo PDO
     * @param $config Configuration
     *
     */
    private static function dropTable($pdo, $tableName) {
        try {            
            $stmt = $pdo->prepare("DROP TABLE $tableName");
            $stmt->execute();
           
        } catch (Exception $e) {
            throw new Exception("Problem deleting the table $tableName. ".$e->getMessage());
        }
        return true;
    }
    

    /*
     * @param $pdo PDO
     * @param $config Configuration
     *
     */
    public static function checkTables($pdo, $config) {
        $resp = true;      
        try {
            
            $resp = $resp && Persistence::checkHitTable($pdo, $config);
            $resp = $resp && Persistence::checkOptionsTable($pdo, $config);
            $resp = $resp && Persistence::checkFromTable($pdo, $config);
            $resp = $resp && Persistence::checkUrlTable($pdo, $config);
        } catch (Exception $e) {
            return false;
        }        
        return $resp;

    }

    /*
     * @param $pdo PDO
     * @param $config Configuration
     */
    public static function checkFromTable($pdo, $config) {
        try {
            $db_from_table = $config->getFromTableName();
            $stmt = $pdo->prepare("SELECT * FROM $db_from_table WHERE 1==0");
            $stmt->execute();
        } catch (Exception $e) {
            return false;
        }
        return true;
    }
        
    /*
     * @param $pdo PDO
     * @param $config Configuration
     */
    public static function checkUrlTable($pdo, $config) {
        try {
            $db_url_table = $config->getUrlTableName();
            $stmt = $pdo->prepare("SELECT * FROM $db_url_table WHERE 1==0");
            $stmt->execute();            
        } catch (Exception $e) {
            return false;
        }
        return true;
    }
    
   
    
    /*
     * @param $pdo PDO
     * @param $config Configuration
     */
    public static function checkOptionsTable($pdo, $config) {
        try {
            $db_options_table = $config->getOptionsTableName();
            $stmt = $pdo->prepare("SELECT * FROM $db_options_table WHERE 1==0");
            $stmt->execute();
        } catch (Exception $e) {
            return false;
        }
        return true;
    }
    

     

    
    /*
     * @param $pdo PDO
     * @param $config Configuration
     *
     */
    public static function countFrom($pdo, $config, $options = []) {
        if (array_key_exists('from_id', $options)) {
            $ids = [$options['from_id']];
        } else {
            $from_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'No referer info';
            $ids = Persistence::findHitIdsByUrl($pdo, $config, $from_url); 
            if (count($ids)==0) {
                $stmt = $pdo->prepare("INSERT INTO $db_url_table (id, url, count) VALUES (?, ?, 1)");
                $stmt->execute([$from_url, $from_url]);
                $ids = [$from_url];
            }
        }
        foreach ($ids as $from_id) {
            $stmt = $pdo->prepare("UPDATE $db_from_table SET count = count + 1 WHERE id = ? and from_id = ?");
            $stmt->execute([$id, $from_id]);
            if ($stmt->rowCount()==0) {
                $stmt = $pdo->prepare("INSERT INTO $db_from_table (id, from_id, count) VALUES (?, ?, 1)");
                $stmt->execute([$id, $from_id]);
            }
        }
    }
    
    /*
     * @param $pdo PDO
     * @param $config Configuration
     *
     */
    public static function countOptions($pdo, $config, $options = []) {
        $user = null;
        if ($store_user) {
            if (array_key_exists('user', $options)) {
                $user = $options['user'];
            }    
        }
        
        if ($store_time || $store_user) {
            $stmt = $pdo->prepare("INSERT INTO $db_options_table (id, time, user) VALUES (?, ?, ?)");
            $stmt->execute([$id, time(), $user]);
        }        
    }
    
    /*
     * @param $pdo PDO
     * @param $config Configuration
     *
     */
    public static function updateCount($pdo, $config, $options = []) {

        $db_hit_table = $config->getHitTableName();
        $db_options_table = $config->getOptionsTableName();
        $db_from_table = $config->getFromTableName();
        $db_url_table = $config->getUrlTableName();

        $store_from = true;
        $store_time = true;
        $store_user = true;
        
        Persistence::countHit($pdo, $config, $options);
        Persistence::countFrom($pdo, $config, $options);           
        Persistence::countOptions($pdo, $config, $options); 
        
        return true;
    }

    /*
     * @param $pdo PDO
     * @param $config Configuration
     *
     */
    public static function findHitIdsByUrl($pdo, $config, $url) {
        $resp = [];
        try {
            
            $dbtable = $config->getUrlTableName();
            $stmt = $pdo->prepare("SELECT id,url,count FROM $dbtable WHERE url = '$url'");
            if ($stmt->execute()) {
                while ($row = $stmt->fetch()) {
                    $resp[] = $row['id'];
                }
            }
        } catch (Exception $e) {
            throw new Exception("Error executing function 'findHitsByUrl'. ".$e->getMessage());
        }
        return $resp;        
    }


    
    /*
     * @param $pdo PDO
     * @param $config Configuration
     *
     */
    public static function findUrls($pdo, $config, $by = []) {
        $resp = [];
        try {
            $dbtable = $config->getUrlTableName();
            $qdata = [];
            $tquery = [];
            if (array_key_exists('id', $by)) {
                $qdata[] = $by['id'];
                $tquery[] = "id = ?";
            }
            
            if (array_key_exists('url', $by)) {
                $qdata[] = $by['url'];
                $tquery[] = "url = ?";
            }
            
            $sql = "SELECT id,url,count FROM $dbtable";
            if (count($tquery) > 0) {
                $sql = $sql." WHERE ".join(" AND ", $tquery);
            }
            
            $stmt = $pdo->prepare($sql);
            if ($stmt->execute($qdata)) {
                while ($row = $stmt->fetch()) {
                    $resp[] = [$row['id'], $row['url'], $row['count']];
                }
            }
            
        } catch (Exception $e) {
            throw new Exception("Error executing function 'getAllUrls'. ".$e->getMessage());
        }
        return $resp;
    }

    /*
     * @param $pdo PDO
     * @param $config Configuration
     *
     */
    public static function findIdByTimeUser($pdo, $config, $by = []) {
        $resp = [];
        try {
            $dbtable = $config->getOptionsTableName();
            $qdata = [];
            $tquery = [];
            if (array_key_exists('from', $by)) {
                $qdata[] = $by['from'];
                $tquery[] = "time >= ?";
            }
            
            if (array_key_exists('to', $by)) {
                $qdata[] = $by['to'];
                $tquery[] = "time <= ?";
            }
            
            if (array_key_exists('user', $by)) {
                $qdata[] = $by['user'];
                $tquery[] = "user = ?";
            }
            
            $sql = "SELECT id,time,user FROM $dbtable";
            if (count($tquery) > 0) {
                $sql = $sql." WHERE ".join(" AND ", $tquery);
            }
            
            $stmt = $pdo->prepare($sql);
            if ($stmt->execute($qdata)) {
                while ($row = $stmt->fetch()) {
                    $resp[] = [$row['id'], $row['time'], $row['user']];
                }
            }
            
        } catch (Exception $e) {
            throw new Exception("Error executing function 'getAllUrls'. ".$e->getMessage());
        }
        return $resp;
    }
    

    /*
     * @param $pdo PDO
     * @param $config Configuration
     *
     */
    public static function findByFrom($pdo, $config, $by = []) {
        $resp = [];
        try {
            $dbFromtable = $config->getFromTableName();
            $dbUrltable = $config->getUrlTableName();
            $qdata = [];
            $tquery = [];
            
            if (array_key_exists('url', $by) && array_key_exists('id', $by)) {
                $qdata = [$by['url'], $by['id']];
                $tquery = "SELECT f.* FROM  $dbFromtable f,$dbUrltable u WHERE (f.from_id = u.id and f.url = ?) or f.from_id = ?";                
            } else if (array_key_exists('url', $by)) {
                $qdata = [$by['url']];
                $tquery = "SELECT f.* FROM $dbFromtable f,$dbUrltable u where f.from_id = u.id and u.url = ?";
            } else if (array_key_exists('id', $by)) {
                $qdata = [$by['id']];
                $tquery = "SELECT f.* FROM $dbFromtable f where f.from_id = ?";
            } else {
                $qdata = [];
                $tquery = "SELECT f.* FROM $dbFromtable f";
            }
                                    
            $stmt = $pdo->prepare($tquery);
            if ($stmt->execute($qdata)) {
                while ($row = $stmt->fetch()) {
                    $resp[] = [$row['id'], $row['from_id'], $row['count']];
                }
            }
            
        } catch (Exception $e) {
            throw new Exception("Error executing function 'findByFrom'. ".$e->getMessage());
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
        try {

            $dbHitTable = $config->getHitTableName();
            $dbUrlTable = $config->getUrlTableName();
            $stmt = $pdo->prepare("SELECT h.id, u.url, h.count FROM $dbHitTable h, $dbUrlTable u WHERE h.id=u.id");
            if ($stmt->execute()) {
                while ($row = $stmt->fetch()) {
                    $resp[] = [$row[0], $row[1], $row[2]];
                }
            }
            
        } catch (Exception $e) {
            throw new Exception("Error reading the database. Method getCounts().".$e->getMessage());
        }        
        return $resp;
    }

    
    /*
     * @param $pdo PDO
     * @param $config Configuration
     *
     */
    public static function getHitsWithOptions($pdo, $config) {
        $resp = [];
        try {
            
            $dbOptionsTable = $config->getOptionsTableName();
            $stmt = $pdo->prepare("SELECT o.id, o.time, o.user FROM $dbOptionsTable o");
            if ($stmt->execute()) {
                while ($row = $stmt->fetch()) {
                    $resp[] = ['id'=>$row[0], 'time'=>$row[1], 'user'=>$row[2]];
                }
            }
            
        } catch (Exception $e) {
            throw new Exception("Error reading the database. Method getCounts().".$e->getMessage());
        }
        return $resp;
    }
    
    /*
     * @param $pdo PDO
     * @param $config Configuration
     *
     */    
    public static function getCountsById($pdo, $config) {
        return "Works";
    }
}


            
