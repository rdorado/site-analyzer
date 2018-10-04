<?php
/**
 *
 * (c) Ruben Dorado <ruben.dorados@google.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//namespace SiteAnalyzer;
include 'Configuration.php';
include 'Persistence.php';

/**
 * class SiteAnalyzer
 *
 * @package   SiteAnalyzer
 * @author    Ruben Dorado <ruben.dorados@gmail.com>
 * @copyright 2018 Ruben Dorado
 * @license   http://www.opensource.org/licenses/MIT The MIT License
 */
class SiteAnalyzer{


    /*
     * @param 
     */
    static function count($pdo=null)
    {
        $config = new Configuration("site-analyzer.ini", isset($pdo));
        if($pdo==null){
            $pdo = Persistence::getPDO($config);
        }
        return Persistence::updateCount($pdo,$config);        
    }


    /*
     * @param $format string, one of [php-array, xml, json, txt-csv]
     */
    public static function getStats($pdo=null, $renderer=null)
    {         
        $config = new Configuration("site-analyzer.ini", isset($pdo));
        if($pdo==null){
            $pdo = Persistence::getPDO($config);
        }
        $data = Persistence::getCounts($pdo,$config);
        return $data;
    } 


    /*
     * @param
     */
    public static function getABTest()
    {

    } 


    /**********************************************************
         Helper methods
     **********************************************************/

    /*
     * @param
     */
    private static function loadconfig()
    {
        //if (!$config = parse_ini_file('itemcounter.ini', TRUE)) throw new exception('Unable to open configuration file "itemcounter.ini".'); 
          
        return $config;
    }



}

