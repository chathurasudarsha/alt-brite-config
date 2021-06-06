<?php

namespace Brite\Config;

class IniConfig extends Config {
    public function __construct($path, $section) {
        $config = parse_ini_file($path, true);
        
        $assoc = array();
        
        // For each section create a named entry in assoc
        foreach ($config as $name => $values) {
            $parts = explode(':', $name);
            
            if (count($parts) > 1) {
                $assoc[$parts[0]] = array('extends' => $parts[1]);
            }
            
            // And set that section's dot-notated entries into assoc
            foreach ($values as $k => $v) {
                $this->_setDotNotation($assoc[$parts[0]], $k, $v);
            }
        }
        
        // Then deal with the extending
        if (!isset($assoc[$section])) {
            throw new \InvalidArgumentException("No registered configuration called $section");
        }
        $array = $assoc[$section];
        $out   = $array;
        
        while(isset($array['extends'])) {
            $array = $assoc[$array['extends']];
            $out = $this->_recursiveArrayMerge($array, $out);
        }
        
        $this->_config = $out;
    }
}
