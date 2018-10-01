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
    static function count($pdo=NULL)
    {
        $config = null;
        try{
            $config = new Configuration("site-analyzer.ini", isset($pdo));
        }
        catch(ConfigurationException $e){
            trigger_error("[SiteAnalyzer] ".$e->getMessage(), E_USER_WARNING);
            return;
        } 

        if($pdo==NULL){
            try{ 
                $pdo = Persistence::getPDO($config);
            }
            catch(DatabaseException $e){
                trigger_error("[SiteAnalyzer] ".$e->getMessage(), E_USER_WARNING);
                return;
            }
        }

        try{
            Persistence::updateCount($pdo, $config);
        }
        catch(DatabaseException $e){
            trigger_error("[SiteAnalyzer] ".$e->getMessage(), E_USER_WARNING);
            return;
        }
        
    }


    /*
     * @param
     */
    public static function getStats()
    {

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

