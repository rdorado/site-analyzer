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
    public static function count($options = [])
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
    public static function resetDatabase($options = [])
    {
        $config = SiteAnalyzer::loadConfig();
        $pdo = SiteAnalyzer::getPDO($config, $options);
        
        Persistence::deleteDatabase($pdo, $config);
        Persistence::crateDatabase($pdo, $config);
    }
    
    /*
     * @param $format string, one of [php-array, xml, json, txt-csv]
     */
    public static function loadConfig($pdoProvided = FALSE)
    {
        try {
            $config = new Configuration("../../../../site-analyzer.ini", $pdoProvided);
        } catch (Exception $e) {
            try {
                $config = new Configuration("site-analyzer.ini", $pdoProvided); 
            } catch (Exception $e) {
                throw new Exception("Config file not found.");
            }
        }        
        return $config;
    }

    /*
     * @param 
     */
    public static function getPDO($config, $options)
    {
        if (array_key_exists("pdo",$options)) {
            return $options["pdo"];
        }         
        return  Persistence::getPDO($config);
    }
    
    /*
     * @param $format string, one of [php-array, xml, json, txt-csv]
     */
    public static function getStats($options = [])
    {   
        $config = SiteAnalyzer::loadConfig();
        $pdo = SiteAnalyzer::getPDO($config, $options);
        
        $data = Persistence::getCounts($pdo, $config);
        return $data;
    } 

    /*
     * @param
     */
    public static function groupHitsByTime($options = [])
    {
        $config = SiteAnalyzer::loadConfig();
        $pdo = SiteAnalyzer::getPDO($config, $options);
        
        $data = OptionsDAO::getHitsWithOptions($pdo, $config);
        $resp = [];
        foreach ($data as $row) {
            $tmp = [$row[0]];
            $tmp = array_merge($tmp, getdate($row[1]));
            $resp[] = $tmp;
        }        
        return $resp;        
    }
    
    /*
     * @param
     */
    public static function groupHitsByUser($options = [])
    {
        $config = SiteAnalyzer::loadConfig();
        $pdo = SiteAnalyzer::getPDO($config, $options);
        
        $data = OptionsDAO::getHitsWithOptions($pdo, $config);        
        $count = [];
        foreach ($data as $row) {
            if (array_key_exists($row[2], $count)) {
                $count[$row[2]]++;
            } else {
                $count[$row[2]] = 1;
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
    public static function getTransitionMatrix($options = [])
    { 
        $config = SiteAnalyzer::loadConfig();
        $pdo = SiteAnalyzer::getPDO($config, $options);
        
        $targetCounts = Persistence::findByFrom($pdo, $config);        
        $data = Matrix::submatrix($targetCounts, [1, 0, 2]);        
        $data = Matrix::toSquareMatrix($data, 0, 1, 2);
        $labels = Matrix::arrayToMatrix($data["labels"]);
        $data = $data["data"];
        $data = Matrix::toBinary($data);
        
        if (array_key_exists("level", $options)) {
            $level = $options["level"];
        }
        else {
            $level = count($labels) - 1;
        }
        
        $result = $data;
        for ($i=1;$i<$level;$i++) {
            $tmp = Matrix::multiply($result, $data);
            $result = Matrix::sum($result, $tmp);
        }
        
        $result = Matrix::toBinary($result);
        return ["labels"=>$labels, "data"=>$result];
    } 
    
    /*
     * @param
     */
    public static function getPathCountMatrix($options = [])
    {
        
        $config = SiteAnalyzer::loadConfig();
        $pdo = SiteAnalyzer::getPDO($config, $options);
        
        $targetCounts = Persistence::findByFrom($pdo, $config);
        $data = Matrix::submatrix($targetCounts, [1, 0, 2]);
        $data = Matrix::toSquareMatrix($data, 0, 1, 2);
        $labels = Matrix::arrayToMatrix($data["labels"]);
        $data = $data["data"];
        $data = Matrix::toBinary($data);
        
        if (array_key_exists("level", $options)) {
            $level = $options["level"];
        }
        else {
            $level = count($labels) - 1;
        }
        
        $result = $data;
        for ($i=1;$i<$level;$i++) {
            $tmp = Matrix::multiply($result, $data);
            $result = Matrix::sum($result, $tmp);
        }
        
        return ["labels"=>$labels, "data"=>$result];
        
    }

    /*
     * @param
     */
    public static function getTransitionCounts($options = [])
    {
        $config = SiteAnalyzer::loadConfig();
        $pdo = SiteAnalyzer::getPDO($config, $options);
        
        $targetCounts = Persistence::findByFrom($pdo, $config);
        
        $data = Matrix::submatrix($targetCounts, [1, 0, 2]);
        $data = Matrix::toSquareMatrix($data, 0, 1, 2);
        $labels = Matrix::arrayToMatrix($data["labels"]);
        
        return ["data"=>$data["data"], "labels"=>$labels];
    } 
    
    /*
     * @param
     */
    public static function performABTest($tests, $options = [])
    {
        $config = SiteAnalyzer::loadConfig();
        $pdo = SiteAnalyzer::getPDO($config, $options);
        
        $testCounts = Persistence::getCountsFromTest($pdo, $config, $tests);  
        $targetCounts = Persistence::getFromByTest($pdo, $config, $tests);
        $result = Statistics::ABtest($testCounts, $targetCounts);
        
        return $result;
    } 

    /*
     * @param
     */
    public static function findVisitTimeProfiles($nprofiles, $options = [])
    {
        $config = SiteAnalyzer::loadConfig();
        $pdo = SiteAnalyzer::getPDO($config, $options);
        $table = OptionsDAO::getHitsWithOptions($pdo, $config);
        
        
        $data = [];
        foreach ($table as $row) {
            $tmp = getdate($row[1]);
            $data[] = [$tmp['weekday'], $tmp['hours']];
        }
        
        
        $cdata = new CategoricalDataset($data);
        $cdata->setEncodedFeatures([0, 1]);
        $tdata = $cdata->encode();
        print( SiteAnalyzer::transform($tdata, "html") );
        $clusters = ML::kmeans($tdata, $nprofiles);        
        
        return [$clusters];
    }
}

