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
 * class SiteAnalyzer
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
            $result[] = tmp;
        }
              
        return $result;
    }

    /*
     * @param
     */
    public static function toSquareMatrix($matrix, $ncol, mcol, $val, $sparse = False)
    {
        $n = count($matrix);
        if ($n == 0) return [];
        
        $labels = {};
        $id = 0;
        $data = [];
        for ($i=0; $i<$n; $i++) {              
            $nid = $labels[$matrix[$i][$ncol]];                
            $mid = $labels[$matrix[$i][$mcol]];
            $val = $matrix[$i][$val];
            $data[] = [$nid, $mid, $val];
        }
        if ($sparse) return ["data" => $data, "labels" => $labels];
        
        $nlab = count($labels);
        $result = [];
        for ($i=0; $i<$nlab; $i++) {           
            $tmp = array_fill(0, $nlab, 0);
            $result[] = $tmp;
        }
        for ($i=0; $i<$n; $i++) {           
            $result[$data[0]][$data[1]] = $data[2];
        }
        return ["data" => $result, "labels" => $labels];
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
        
        if ($n2 !== $m1) throw new Exception("Incompatible matrices: matrix1.columns != matrix2.rows");

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
    public static function power($matrix, $n)
    {
        if($n <= 1) return $matrix;        
        $resp = multiply($matrix, $matrix);
        if($n == 2) return $resp;
        for($i=2; $i<$n; $i++) {
            $resp = multiply($resp, $matrix);
        }
        return $resp;
    }

}
