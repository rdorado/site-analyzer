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
 * class ML
 *
 * @package   SiteAnalyzer
 * @author    Ruben Dorado <ruben.dorados@gmail.com>
 * @copyright 2018 Ruben Dorado
 * @license   http://www.opensource.org/licenses/MIT The MIT License
 */
class ML
{
    
    /*
     * @param
     */
    public static function kmeans($data, $nclusters, $categorical = [])
    {
        $resp = [];
        $finished = false;
        $niter = 0;
        $maxiter = 100;
        $npoints = count($data);
        if ($npoints <= 0) throw new \Exception("Not enough data. ");    
        $ndimensions = count($data[0]);
        $centroids = [];
        for ($i=0;$i<$nclusters;$i++){
            $centroids[] = $data[$i];
        }
        //$centroids = self::initCentroids($nclusters, $ndimensions, function(){return rand(0,100)/100;});   
        while (!$finished && $niter < $maxiter) {
            // Assign each one of the points to one centroid   
            $niter++;
            $nresp = [];
            for ($j = 0; $j < $npoints; $j++) {        
                $best = -1;
                $bdist = INF;
                for ($i = 0; $i < $nclusters; $i++) {
                    $ndist = self::eclideanDistance($data[$j], $centroids[$i]);
                    if($bdist > $ndist) {
                        $bdist = $ndist;
                        $best = $i;
                    }            
                }
                $nresp[] = $best;
                
            }
        
            // Check change 
 
            $finished = true;
            if (count($resp) > 0) {
                for ($j=0; $j < $npoints; $j++) {        
                    if ($resp[$j]!==$nresp[$j]) {
                        $finished = false;
                        break;
                    }
                }
            } else {
                $finished = false;
            }
            $resp = $nresp;
            var_dump($resp);    
            // Recalculate the centroids
            $centroids = self::initCentroids($nclusters, $ndimensions, function(){return 0;});
            $counts = array_fill(0, $nclusters, 0);
            for ($j = 0; $j < $npoints; $j++) {    
                $centroids[$resp[$j]] = Matrix::sumArray($centroids[$resp[$j]], $data[$j]);
                $counts[$resp[$j]]++;            
            }
            $centroids = self::normalizeCentroids($centroids, $counts);
        }
        return [$resp];
    }

    
    /*
     * @param
     */
    public static function normalizeCentroids($centroids, $counts)
    {
        $resp = [];
        $n = count($centroids);
        $d = count($centroids[0]);
        for ($i=0;$i<$n;$i++) {
            $tmp = [];
            for ($j=0;$j<$d;$j++){
                $tmp[] = $centroids[$i][$j]/$counts[$i];
            }
            $resp[] = $tmp;
        }
        return $resp;
    }
    
    /*
     * @param
     */
    public static function initCentroids($nclusters, $ndimensions, $fvalue) 
    {
        $resp = [];
        for ($i = 0; $i < $nclusters; $i++) {
            $centroid = [];
            for ($d = 0; $d < $ndimensions; $d++) {
                $centroid[] = $fvalue();
            }
            $resp[] = $centroid;
        }
        return $resp;
    }

    /*
     * @param
     */
    public static function eclideanDistance($p1, $p2) {
       $len = count($p1);
       $acum = 0;
       for($i=0; $i<$len; $i++) {
           $acum += ($p1[$i] - $p2[$i])**2;
       }
       return sqrt($acum);
    }
    
}
