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
 * class HitDAO
 *
 * @package   SiteAnalyzer
 * @author    Ruben Dorado <ruben.dorados@gmail.com>
 * @copyright 2018 Ruben Dorado
 * @license   http://www.opensource.org/licenses/MIT The MIT License
 */
class HitDAO
{

    /*
     * @param $pdo PDO
     * @param $config Configuration
     */
    public static function checkHitTable($pdo, $config) {
        try {
            $db_hit_table = $config->getHitTableName();
            $stmt = $pdo->prepare("SELECT * FROM $db_hit_table WHERE 1==0");
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
    public static function countHit($pdo, $config, $options = []) {
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
        
        if (array_key_exists('id', $options)) {
            $id = $options['id'];
        } else {
            $id = $url;
        }

        $stmt = $pdo->prepare("UPDATE $db_hit_table SET count = count + 1 WHERE id = ?");
        $stmt->execute([$id]);
        if ($stmt->rowCount()==0) {
            $stmt = $pdo->prepare("INSERT INTO $db_hit_table (id, count) VALUES (?, 1)");
            $stmt->execute([$id]);
        }

        $stmt = $pdo->prepare("UPDATE $db_url_table SET count = count + 1 WHERE id = ? and url = ?");
        $stmt->execute([$id, $url]);
        if ($stmt->rowCount()==0) {
            $stmt = $pdo->prepare("INSERT INTO $db_url_table (id, url, count) VALUES (?, ?, 1)");
            $stmt->execute([$id, $url]);
        }
        
    }
    
        
    /*
     * @param $pdo PDO
     * @param $config Configuration
     *
     */
    public static function getAllHits($pdo, $config) {
        $resp = [];
        try {
            $dbtable = $config->getHitTableName();
            $stmt = $pdo->prepare("SELECT id,count FROM $dbtable");
            if ($stmt->execute()) {
                while ($row = $stmt->fetch()) {
                    $resp[] = [$row['id'], $row['count']];
                }
            }
            
        } catch (Exception $e) {
            throw new Exception("Error executing function 'getAllHits'. ".$e->getMessage());
        }
        return $resp;        
    }


}
