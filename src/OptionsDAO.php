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
 * class OptionsDAO
 *
 * @package   SiteAnalyzer
 * @author    Ruben Dorado <ruben.dorados@gmail.com>
 * @copyright 2018 Ruben Dorado
 * @license   http://www.opensource.org/licenses/MIT The MIT License
 */
class OptionsDAO
{

    /*
     * @param $pdo \PDO
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
     * @param $pdo \PDO
     * @param $config Configuration
     *
     */
    public static function countOptions($pdo, $config, $id, $options = []) {
        $user = null;
        $db_options_table = $config->getOptionsTableName();
                
        if (array_key_exists('user', $options)) {
            $user = $options['user'];   
        }
                
        $stmt = $pdo->prepare("INSERT INTO $db_options_table (id, time, user) VALUES (?, ?, ?)");
        $stmt->execute([$id, time(), $user]);       
    }
    
    /*
     * @param $pdo \PDO
     * @param $config Configuration
     *
     */
    public static function findIdByTimeUser($pdo, $config, $by = []) {
        $resp = [];
        $dbtable = $config->getOptionsTableName();
        $qdata = [];
        $tquery = [];
        $keySql = [ 'from'=>"time >= ?", 
                    'to'=>"time <= ?", 
                    'user'=>"user = ?" 
        ];
        
        for ($keySql as $key => $sql){
            if (array_key_exists($key, $by)) {
                $qdata[] = $by[$key];
                $tquery[] = $sql;
            }
        }
                        
        $sql = "SELECT id,time,user FROM $dbtable";
        if (count($tquery) > 0) {
            $sql = $sql." WHERE ".join(" AND ", $tquery);
        }
            
        $stmt = $pdo->prepare($sql);
        $stmt->execute($qdata);
        while ($row = $stmt->fetch()) {
            $resp[] = [$row['id'], $row['time'], $row['user']];
        }
            
        return $resp;
    }
    
    /*
     * @param $pdo \PDO
     * @param $config Configuration
     *
     */
    public static function getHitsWithOptions($pdo, $config) {
        $resp = [];
            
        $dbOptionsTable = $config->getOptionsTableName();
        $stmt = $pdo->prepare("SELECT o.id, o.time, o.user FROM $dbOptionsTable o");
        $stmt->execute();
        while ($row = $stmt->fetch()) {
            $resp[] = ['id'=>$row[0], 'time'=>$row[1], 'user'=>$row[2]];
        }
            
        return $resp;
    }
    
}
