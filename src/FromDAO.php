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
/**
 * class FromDAO
 *
 * @package   SiteAnalyzer
 * @author    Ruben Dorado <ruben.dorados@gmail.com>
 * @copyright 2018 Ruben Dorado
 * @license   http://www.opensource.org/licenses/MIT The MIT License
 */
class FromDAO
{

    /*
     * @param $pdo \PDO
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
     * @param $pdo \PDO
     * @param $config Configuration
     *
     */
    public static function countFrom($pdo, $config, $id, $options = []) {
        $db_url_table = $config->getUrlTableName();
        $db_from_table = $config->getFromTableName(); 
        
        if (array_key_exists('from_id', $options)) {
            $ids = [$options['from_id']];
        } else {
            $from_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'No referer info';
            $ids = UrlDAO::findHitIdsByUrl($pdo, $config, $from_url); 
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
     * @param $pdo \PDO
     * @param $config Configuration
     *
     */
    public static function findByFrom($pdo, $config, $by = []) {
        $resp = [];
        try {
            $dbFromtable = $config->getFromTableName();
            $dbUrltable = $config->getUrlTableName();
            $qdata = [];
            $tquery = "";
            
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
     * @param $pdo \PDO
     * @param $config Configuration
     *
     */
    public static function findAll($pdo, $config) {
        $resp = [];
        try {
            $dbFromtable = $config->getFromTableName();
            $query = "SELECT f.* FROM  $dbFromtable";
            
            
            $stmt = $pdo->prepare($query);
            if ($stmt->execute()) {
                while ($row = $stmt->fetch()) {
                    $resp[] = [$row['id'], $row['from_id'], $row['count']];
                }
            }
            
        } catch (Exception $e) {
            throw new Exception("Error executing function 'findAll'. ".$e->getMessage());
        }
        return $resp;
    }
    
    
}
