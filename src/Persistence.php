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
            
            $resp = $resp && HitDAO::checkHitTable($pdo, $config);
            $resp = $resp && OptionsDAO::checkOptionsTable($pdo, $config);
            $resp = $resp && FromDAO::checkFromTable($pdo, $config);
            $resp = $resp && UrlDAO::checkUrlTable($pdo, $config);
        } catch (Exception $e) {
            return false;
        }        
        return $resp;

    }
       
    /*
     * @param options
     *
     */
    public static function getURL($config, $options = []) {
        if (array_key_exists('url', $options)) {
            $url = $options['url'];
        } else if (array_key_exists('HTTP_HOST', $_SERVER)) {
            $url = "http://".$_SERVER['HTTP_HOST'];
            if (array_key_exists('REQUEST_URI', $_SERVER)) {
                $url = $url.$_SERVER['REQUEST_URI'];
            }               
        } else {
            $url = "No Info";
        }

        if ($config->getRemoveQueryString()) {
            $url = preg_replace('/\?.*/', '', $url);
        }
        return $url;
    }
    
    /*
     * @param $pdo PDO
     * @param $config Configuration
     *
     */
    public static function updateCount($pdo, $config, $options = []) {
        $url = Persistence::getUrl($config, $options);
        
        if (array_key_exists('id', $options)) {
            $id = $options['id'];
        } else {
            $id = $url;
        }    
        
        HitDAO::countHit($pdo, $config, $id, $url);
        FromDAO::countFrom($pdo, $config, $id, $options);           
        OptionsDAO::countOptions($pdo, $config, $id, $options); 
        
        return true;
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
     */
    public static function checkUrlTable($pdo, $config) {
        return UrlDAO::checkUrlTable($pdo, $config);
    }
    
    /*
     * @param $pdo PDO
     * @param $config Configuration
     *
     */    
    public static function getCountsById($pdo, $config) {
        return "Works ".$pdo." ".$config;
    }
    
}


            
