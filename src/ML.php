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
    function kmeans($data, $nclusters)
    {
        $resp = [];
        $finished = false;
        $npoints = count($data);
        if (count($npoints) <= 0) throw new Exception("Not enough data. ");    
        $ndimensions = count($npoints[0]);
        $centroids = initCentroids($nclusters, $ndimensions, function(){return rand(0,100)/100;});   
        while (!$finished) {
    
            // Assign each one of the points to one centroid        
            $nresp = [];
            for ($j = 0; $j < $npoints; $j++) {        
                $best = -1;
                $bdist = INF;
                for ($i = 0; $i < $nclusters; $i++) {
                    $ndist = eclideanDistance($npoints[$j], $nclusters[$i]);
                    if($bdist > $ndist) {
                        $bdist = $ndist;
                        $best = $i;
                    }            
                }
                $nresp[] = $best;
            }
        
            // Check change 
            if(count($resp)!=0) {
                $finished = true;
                for ($j = 0; $j < $npoints; $j++) {        
                    if($resp[$j]!==$nresp[$j]){
                        $finished = false;
                        break;
                    }
                }
                $resp = $nresp;
            }
                
            /** Recalculate the centroids*/
            $centroids = initCentroids($nclusters, $ndimensions, function(){return 0;});
            $counts = array_fill(0, $nclusters, 0);
            for ($j = 0; $j < $npoints; $j++) {              
                sumCentroid($centroids[$resp[$j]], $data[$j]);
                $counts[$resp[$j]]++;            
            }
            normalizeCentroids($centroids, $counts);
        }
    }

    /*
     * @param
     */
    function initCentroids($nclusters, $ndimensions, $fvalue) 
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
    function eclideanDistance($p1, $p2) {
       $len = count($p1);
       $acum = 0;
       for($i=0; $i<$len; $i++) {
           $acum += ($p1[$i] - $p2[$i])**2;
       }
       return sqrt($acum);
    }
    
}
