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
 * class Matrix
 *
 * @package   SiteAnalyzer
 * @author    Ruben Dorado <ruben.dorados@gmail.com>
 * @copyright 2018 Ruben Dorado
 * @license   http://www.opensource.org/licenses/MIT The MIT License
 */
class Matrix
{

    /*
     * @param
     */
    public static function submatrix($matrix, $columns)
    {
        $n = count($matrix);
        if ($n == 0) return [];
       
        $m = count($matrix[0]);
        if ($m == 0) return $matrix;
       
        $result = [];       
        for ($i=0; $i<$n; $i++) {
            $tmp = [];
            foreach ($columns as $col) {
                $tmp[] = $matrix[$i][$col];
            }
            $result[] = $tmp;
        }
              
        return $result;
    }

    /*
     * @param
     */
    public static function toSquareMatrix($matrix, $ncol, $mcol, $vcol, $sparse = False)
    {
        $n = count($matrix);
        if ($n == 0) return [];
        
        $labels = [];
        $id = 0;
        $data = [];
        for ($i=0; $i<$n; $i++) {  
            if (!array_key_exists($matrix[$i][$ncol],$labels)) {
                $labels[$matrix[$i][$ncol]] = $id++;
            }      
            $nid = $labels[$matrix[$i][$ncol]];
            if (!array_key_exists($matrix[$i][$mcol],$labels)) {
                $labels[$matrix[$i][$mcol]] = $id++;
            }
            $mid = $labels[$matrix[$i][$mcol]];
            $val = $matrix[$i][$vcol];
            $data[] = [$nid, $mid, $val];
        }
        if ($sparse) return ["data" => $data, "labels" => $labels];
        $nlab = count($labels);
        $result = [];
        for ($i=0; $i<$nlab; $i++) {           
            $tmp = array_fill(0, $nlab, 0);
            $result[] = $tmp;
        }
        foreach ($data as $row) {           
            $result[$row[0]][$row[1]] = $row[2];
        }
        return ["data" => $result, "labels" => $labels];
    }
    
    /*
     * @param
     */
    public static function sum($matrix1, $matrix2)
    {
        $result = [];
        $n = count($matrix1);        
        if ($n != count($matrix2)) throw new \Exception("Summing non compatible matrices. ");
        $m = count($matrix1[0]);
        for ($i=0;$i<$n;$i++) {
            $tmp = [];
            for($j=0;$j<$m;$j++) {
                $tmp[] = $matrix1[$i][$j] + $matrix2[$i][$j];
            }
            $result[] = $tmp;
        }
        return $result;
    }
    
    
    /*
     * @param
     */
    public static function multiply($matrix1, $matrix2)
    {
        $n1 = count($matrix1);
        $m1 = count($matrix1[0]);
        $n2 = count($matrix2);        
        $m2 = count($matrix2[0]);
        
        if ($n2 !== $m1) throw new \Exception("Incompatible matrices: matrix1.columns != matrix2.rows");

        $result = [];
        for ($i=0; $i<$n1; $i++) {           
            $result[] = array_fill(0, $m2, 0);                    
        }
        for ($i=0; $i<$n1; $i++) {
            for ($j=0; $j<$m2; $j++) {
                 for ($k=0; $k<$n2; $k++) {
                     $result[$i][$j] += $matrix1[$i][$k]*$matrix2[$k][$j];
                 }
            }
        }        
        return $result;
    }

    /*
     * @param
     */
    public static function toBinary($matrix) {
        $resp = [];
        foreach ($matrix as $row) {
            $tmp = [];
            foreach ($row as $dat) {
                $tmp[] = $dat == 0 ? 0 : 1;
            }
            $resp[] = $tmp;
        }
        return $resp;
    }
    
    /*
     * @param
     */
    public static function arrayToMatrix($array) {
        $resp = [];
        foreach ($array as $key => $val) {
            $resp[] = [$key, $val];
        }
        return $resp;
    }
    
    /*
     * @param
     */
    public static function power($matrix, $n)
    {
        if($n <= 1) return $matrix;        
        $resp = self::multiply($matrix, $matrix);
        if($n == 2) return $resp;
        for($i=2; $i<$n; $i++) {
            $resp = self::multiply($resp, $matrix);
        }
        return $resp;
    }
    
    /*
     * @param
     */
    public static function sumArray($array1, $array2) {
        $resp = [];
        $n = min([count($array1), count($array2)]);
        for ($i=0;$i<$n;$i++){
            $resp[] = $array1[$i] + $array2[$i];
        }
        return $resp;
    }

}
