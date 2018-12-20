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
 
    /**
     * @var array
     */
    protected $data;    
    
    /**
     * @var array
     */
    protected $sortedEncodedFeatures;        

    /**
     * @var array
     */
    protected $encodedValues;      
   
    /**
     * @var array
     */
    protected $featEncode;
    
    /**
     * @var array
     */
    protected $featIndexMap;
    
    /*
     * @param
     */        
    public function __construct($data) 
    {
        $this->data = $data;
    }
    
    /*
     * @param
     */    
    public function setEncodedFeatures($array) 
    {
        sort($array);
        $this->encodedValues = [];
        $this->sortedEncodedFeatures = $array;
        foreach($this->sortedEncodedFeatures as $col){
            $vals = $this->getUniqueValues($col);
            $this->encodedValues[$col] = $vals;
            $this->featIndexMap[$col] = count($vals);
            $this->featEncode[$col] = $this->encodeFeature($vals);
            //$this->featDecode[$col] = function($val, $arr){ return $this->getDecodedFeature($val, $arr, ); }
            //$this->newEncodedSize += count($vals)-1;
        }
        
        /*for ($i=0;$i<$this->newEncodedSize:$i++) {
            
        }*/
    }   
    
    /*
     * @param
     */
    private function getUniqueValues($col) 
    {
        $tmp = Matrix::getColumn($this->data, $col);
        $n = count($tmp);
        $resp = [];
        for ($i=0; $i<$n; $i++) {
            if (!in_array($tmp[$i], $resp)) {
                $resp[] = $tmp[$i];
            }
        }
        $resp = array_unique($resp);
        return $resp;
    }
    
    /*
     * @param
     */
    private function encodeFeature($array) 
    {
        $size = count($array);
        $resp = [];
        for ($i=0;$i<$size;$i++) {
            $tmp = array_fill(0, $size, 0);
            $tmp[$i] = 1;  
            $resp[$array[$i]] = $tmp;
        }
        return $resp;
    }
    
    /*
     * @param
     */  
    public function encode(){
        $transformer  = [];
        $n = count($this->data);
        $ndim = count($this->data[0]);
        for ($j=0; $j<$ndim; $j++) {
            $transformer[] = function($val){ return [$val]; };
        }
        foreach($this->sortedEncodedFeatures as $col) {
            $transformer[$col] = function ($val) use ($col) { return $this->featEncode[$col][$val]; };
        }
        
        $ndata = [];
        for ($i=0; $i<$n; $i++) {
            $npoint = [];
            for ($j=0; $j<$ndim; $j++) {
                $npoint = array_merge($npoint, $transformer[$j]($this->data[$i][$j]));
            }            
            $ndata[] = $npoint;
        }
        return $ndata;
    }
 
    /*
     * @param
     */
    function getLabelsAsArray()
    {
        $resp = [];
        $len = count($this->data[0]);
        for ($i=0; $i<$len; $i++) {
            if (isset($this->encodedValues[$i])) {
                $resp = array_merge($resp, $this->encodedValues[$i]);
            } else {
                $resp[] = "";
            }
        }
        return $resp;
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
