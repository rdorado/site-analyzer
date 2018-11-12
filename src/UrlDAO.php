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
 * class UrlDAO
 *
 * @package   SiteAnalyzer
 * @author    Ruben Dorado <ruben.dorados@gmail.com>
 * @copyright 2018 Ruben Dorado
 * @license   http://www.opensource.org/licenses/MIT The MIT License
 */
class UrlDAO
{

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
    
}
