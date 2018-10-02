<?php
/**
 *
 * (c) Ruben Dorado <ruben.dorados@google.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//namespace SiteAnalyzer;


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

    public static function getCountsById($pdo, $config){
        return "Works";
    }
}


