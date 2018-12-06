



class CategoricalDataset
{
    init($data){
    
    }
    
    function setEncodedFeatures($array){
        $array = sort($array);
        $this->encodedValues = [];
        $this->sortedEncodedFeatures = $array;
        for($feat in $this->sortedEncodedFeatures){
            $vals = getUniqueValues($this->sortedEncodedFeatures, $feat);
            $this->encodedValues[] = $vals;
            $this->encodedFeatMapSize[$feat] = count($vals);
        }
    }   
    
    function encode(){
        $transformer  = [];
        $ndata = [];
        for ($j=0; $j<$ndim; $j++) {
            $transformer[] = function($val){ return $val; };
        }
        foreach( as $key => $val) {
            $transformer[$key] = function($val) { return $this->featEncode[$key][$val]; };
        }
        $ndata = [];
        for ($i=0; $i<$npoints; $i++) {
            $npoint = [];
            for ($j=0; $j<$ndim; $j++) {
                $npoint[] = $transformer[$j]($data[$i][$j]);
            }
            $ndata[] = $npoint;
        }
        $return $ndata;
    }
    
    function decode($ndata){
        $resp = [];
        for ($i=0; $i<$npoints; $i++) {
            //$point = array_fill(0, $nexpdim; null);
            $point = array($ndata[$i]);
            foreach ( as $key => $val) {
                $this->featDecode[$key]($point);            
            }
            $point = $this->removeNulls($point);
            $resp[] = $point;
        }
        return $resp;
    }
    
}
