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
 * class Statistics
 *
 * @package   SiteAnalyzer
 * @author    Ruben Dorado <ruben.dorados@gmail.com>
 * @copyright 2018 Ruben Dorado
 * @license   http://www.opensource.org/licenses/MIT The MIT License
 */
class Statistics
{
    
    /*
     * @param
     */
    public static function ABtest($testCounts, $targetCounts, $options)
    {
        $ntotal = 0;
        $resp  = [];
        foreach($testCounts as $testCount) {
            $ntotal += $testCount[1];
        }
        foreach($testCounts as $testCount) {
            foreach ($targetCounts as $targetCount) {
                if ($testCount[0] === $targetCount[1]) {
                    $resp[] = [$testCount[0], $testCount[1], $testCount[1]/$ntotal, $targetCount[2], $targetCount[2]/$testCount[1],$targetCount[2]/$ntotal];
                }                
            }
            
        }
        return $resp;
    }
    
}
