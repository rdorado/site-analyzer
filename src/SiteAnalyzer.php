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
 * class SiteAnalyzer
 *
 * @package   SiteAnalyzer
 * @author    Ruben Dorado <ruben.dorados@gmail.com>
 * @copyright 2018 Ruben Dorado
 * @license   http://www.opensource.org/licenses/MIT The MIT License
 */
class SiteAnalyzer
{

    
    /*
     * @param 
     */
    static function count($options = [])
    {	
        $config = SiteAnalyzer::loadConfig(array_key_exists('pdo', $options));
        
        if (array_key_exists('pdo', $options)) {            
            $pdo = $options['pdo'];	
        } else {            
            $pdo = Persistence::getPDO($config);
        }
        
        try {
            return Persistence::updateCount($pdo, $config, $options);
        } catch (Exception $e) {
            try {
                Persistence::crateDatabase($pdo, $config);
                return Persistence::updateCount($pdo, $config, $options);
            } catch (Exception $e) {
                throw new Exception("Site Analyzer could connect to the database.".$e->getMessage());
            };
            
        };
            
    }

    
    /*
     * @param $format string, one of [php-array, xml, json, txt-csv]
     */
    public static function loadConfig($pdoProvided = FALSE)
    {
        $config = NULL;
        try {
            $config = new Configuration("../../../../site-analyzer.ini", $pdoProvided);
        } catch (Exception $e) {
            try {
                $config = new Configuration("site-analyzer.ini", $pdoProvided); 
            } catch (Exception $e) {
                throw Exception("Config file not found.");
            }
        }        
        return $config;
    }

    /*
     * @param $format string, one of [php-array, xml, json, txt-csv]
     */
    public static function getStats($pdo = null, $renderer = null)
    {         
        $config = new Configuration("site-analyzer.ini", isset($pdo));
        if ($pdo==null) {
            $pdo = Persistence::getPDO($config);
        }
        $data = Persistence::getCounts($pdo, $config);
        return $data;
    } 


    
    
    /*
     * @param
     */
    public static function groupHitsByTime($criteria, $pdo = null)
    {
        $config = new Configuration("site-analyzer.ini", isset($pdo));
        if ($pdo==null) {
            $pdo = Persistence::getPDO($config);
        }
        $data = Persistence::getHitsWithOptions($pdo, $config);
        $resp = [];
        foreach ($data as $row) {
            $tmp = [$row['id']];
            $tmp = array_merge($tmp, getdate($row['time']));
            $resp[] = $tmp;
        }        
        return $resp;        
    }
    

    /*
     * @param
     */
    public static function groupHitsByUser($pdo = null)
    {
        $config = new Configuration("site-analyzer.ini", isset($pdo));
        if ($pdo==null) {
            $pdo = Persistence::getPDO($config);
        }
        $data = Persistence::getHitsWithOptions($pdo, $config);        
        $count = [];
        foreach ($data as $row) {
            if (array_key_exists($row['user'], $count)) {
                $count[$row['user']]++;
            }
            else {
                $count[$row['user']] = 1;
            }            
        }
        
        $resp = [];
        foreach ($count as $user => $count) {
            $resp[] = [$user, $count];
        }
        return $resp;        
    }
    
    /*
     * @param $format string, one of [php-array, xml, json, txt-csv]
     */
    public static function transform($data, $format)
    {
        if ($format=="html") {
            $resp = "<table style='border-collapse: collapse;border: 1px solid black;'>";
            foreach ($data as $row) {
                $resp .= "<tr style='border: 1px solid black;'>";
                foreach ($row as $cell) {
                    $resp .= "<td style='border: 1px solid black;'>$cell</td>";
                }  
                $resp .= "</tr>";
            }
            return $resp."</table>";
        }
        return $data; 
    } 


    /*
     * @param
     */
    public static function getRelationLinks()
    {

    } 
 

    /*
     * @param
     */
    public static function getABTest()
    {

    } 

}

