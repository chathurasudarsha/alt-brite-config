<?php

namespace Brite\Config;

class PhpConfig extends Config {
    public function __construct($path, $section) {
        $config = array();
        
        @require($path);
        
        $array = $config[$section];
        $out   = $array;
        
        while(isset($array['extends'])) {
            $array = $config[$array['extends']];
            $out = $this->_recursiveArrayMerge($array, $out);
        }
        
        $this->_config = $out;
    }
}
