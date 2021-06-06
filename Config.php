<?php

namespace Brite\Config;

abstract class Config implements \ArrayAccess {
    protected static $_registry = array();
    
    protected $_config = array();
    
    /**
     * Remove the specified config from the internal registry, 
     * removed all registered configs if null is provided
     * 
     * @param string $name the name of the config file to remove
     *  from the internal registry
     */
    public static function unregister($name = null) {
        if ($name === null) {
            foreach (static::$_registry as $name => $conf) {
                static::unregister($name);
            }
        } else {
            unset(static::$_registry[$name]);
        }
    }
    
    /**
     * Register the specified config file using the given name,
     * parsing the given section.
     * 
     * @param string $name name of config file - this name should
     *  be used in subsequent calls to instance() to retrieve the
     *  config. Specify 'default' for the config to be returned
     *  when no name is supplied to instance().
     * @param string $path path to config file on disk, should
     *  be a php file containing an array named $config, or an
     *  ini file
     * @param string $section name of the section which should be
     *  parsed
     * @return \Brite\Config\Config
     */
    public static function register($name, $path, $section) {
        $format = substr(strrchr($path, '.'), 1);
        
        $class  = '\\Brite\\Config\\' . ucfirst($format) . 'Config';
        
        self::$_registry[$name] = new $class($path, $section);
        
        return self::$_registry[$name];
    }
    
    /**
     * Retrieve a config object using the name specified when the
     * config file was registered
     * 
     * @param string $name name of registered config file to
     *  retrieve
     * @throws \InvalidArgumentException
     * @return Config
     */
    public static function instance($name = 'default') {
        if (isset(self::$_registry[$name])) {
            return self::$_registry[$name];
        }
        throw new \InvalidArgumentException("No registered configuration called $name");
    }
    
    /**
     * Create a config object using the file at the path specified,
     * using config values found in the section specified.
     * 
     * @param string $path path to configuration file
     * @param string $section section which the config object should
     *  parse
     */
    abstract public function __construct($path, $section);
    
    /**
     * Get the specified key from the config file, returning default
     * if the key is not found
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed 
     */
    public function get($key, $default = null) {
        return $this->_getDotNotation($this->_config, $key, $default);
    }
    
    /**
     * Set the specified key to the given value
     * 
     * @param string $key
     * @param mixed $value 
     */
    public function set($key, $value) {
        $this->_setDotNotation($this->_config, $key, $value);
    }
    
    public function offsetSet($offset, $value) {
        if (is_null($offset)) {
            $this->_config[] = $value;
        } else {
            $this->_config[$offset] = $value;
        }
    }

    public function offsetExists($offset) {
        return isset($this->_config[$offset]);
    }

    public function offsetUnset($offset) {
        unset($this->_config[$offset]);
    }

    public function offsetGet($offset) {
        return isset($this->_config[$offset]) ?
                $this->_config[$offset] :
                null;
    }
    
    protected function _getDotNotation(&$val, $dotNotationKey, $default = null) {
        $p   = explode('.', $dotNotationKey);
        
        // Resolve key to pointer in given array,
        // returning default if finding something doesn't exist
        $i = 0;
        foreach ($p as $part) {
            if (!is_array($val) || !isset($val[$part])) {
                return $default;
            }
            
            $c = &$val[$part];
            unset($val);
            $val = &$c;
        }
        
        return $val;
    }
    
    protected function _setDotNotation(&$val, $dotNotationKey, $value) {
        $p   = explode('.', $dotNotationKey);
        
        // Resolve key to pointer to element in given array,
        // creating if necesarry
        foreach ($p as $part) {
            if (!is_array($val) || !isset($val[$part])) {
                $val[$part] = array();
            }
            
            $c = &$val[$part];
            unset($val);
            $val = &$c;
        }
        
        $val = $value;
    }
    
    protected function _recursiveArrayMerge($mergeInto, $mergeFrom) {
        if (is_array($mergeInto) && is_array($mergeFrom)) {
            foreach ($mergeFrom as $key => $value) {
                if (isset($mergeInto[$key])) {
                    $mergeInto[$key] = $this->_recursiveArrayMerge($mergeInto[$key], $value);
                } else {
                    $mergeInto[$key] = $value;
                }
            }
        } else {
            $mergeInto = $mergeFrom;
        }
        
        return $mergeInto;
    }
    
}
