<?php
/**
 *
 * (c) Ruben Dorado <ruben.dorados@google.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SiteAnalyzer;
/**
 * class CategoricalDataset
 *
 * @package   SiteAnalyzer
 * @author    Ruben Dorado <ruben.dorados@gmail.com>
 * @copyright 2018 Ruben Dorado
 * @license   http://www.opensource.org/licenses/MIT The MIT License
 */
class CategoricalDataset
{
    
    /*
     * @param
     */        
    function __construct($data) 
    {
        $this->data = $data;
    }
    
    /*
     * @param
     */    
    function setEncodedFeatures($array) 
    {
        $array = sort($array);
        $this->encodedValues = [];
        $this->sortedEncodedFeatures = $array;
        $this->newEncodedSize = count($array);
        foreach($this->sortedEncodedFeatures as $col){
            $vals = $this->getUniqueValues($col);
            $this->encodedValues[] = $vals;
            $this->featIndexMap[$col] = count($vals);
            $this->featEncode[$col] = $this->encodeFeature(count($vals));
            //$this->featDecode[$col] = function($val, $arr){ return $this->getDecodedFeature($val, $arr, ); }
            //$this->newEncodedSize += count($vals)-1;
        }
        
        /*for ($i=0;$i<$this->newEncodedSize:$i++) {
            
        }*/
    }   
    
    /*
     * @param
     */
    function getUniqueValues($col) 
    {
        $resp = [];
        $resp = Matrix::getColumn($data, $col);
        $resp = array_unique($resp);
        return $resp;
    }
    
    
    /*
     * @param
     */
    function encodeFeature($size) 
    {
        $resp = [];
        for ($i=0;$i<$size;$i++) {
            $tmp = array_fill(0, $size, 0);
            $tmp[$i] = 1;  
            $resp[] = $tmp;
        }
        return $resp;
    }
    
    /*
     * @param
     */  
    function encode(){
        $transformer  = [];
        $ndata = [];
        for ($j=0; $j<$ndim; $j++) {
            $transformer[] = function($val){ return [$val]; };
        }
        foreach($this->sortedEncodedFeatures as $col) {
            $transformer[$col] = function($val) { return $this->featEncode[$col][$val]; };
        }
        $ndata = [];
        for ($i=0; $i<$npoints; $i++) {
            $npoint = [];
            for ($j=0; $j<$ndim; $j++) {
                $npoint += $transformer[$j]($data[$i][$j]);
            }
            $ndata[] = $npoint;
        }
        return $ndata;
    }
    
    /*
     * @param
     *
    function decode($ndata){
        $resp = [];
        foreach ($ndata as $row) {             
            $resp[] = $this->decodeRow($row);
        }
        return $resp;
    }

    /*
     * @param
     *     
    function decodeRow($row){
        $resp = [];
        $n = count($row);
        for ($i=0; $i<$n; $i++) {
            $resp[] = $this->decodeFeature($i, $row);
        }
        return $resp;
    }*/
    
}
