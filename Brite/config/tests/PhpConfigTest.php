<?php

require_once 'autoload.php';

use Brite\Config\PhpConfig,
    Brite\Config\ArrayConfig,
    Brite\Config\Config;

class PhpConfigTest extends PHPUnit_Framework_TestCase {
    protected function _newConfig($section) {
        return new PhpConfig(__DIR__ . '/test_config/config.php', $section);
    }
    
    public function testParse() {
        $prod = $this->_newConfig('production');
        $stag = $this->_newConfig('staging');
        $dflt = $this->_newConfig('default');
        
        return compact('prod', 'stag', 'dflt');
    }
    
    /**
     * @depends testParse
     */
    public function testGet($conf) {
        $this->assertEquals('bar', $conf['prod']->get('database.host'), 'Production database.host entry');
        $this->assertEquals('foo1', $conf['prod']->get('database.user'), 'Production database.user entry');
        $this->assertEquals('baz1', $conf['prod']->get('database.pass'), 'Production database.pass entry');
        $this->assertEquals('test@production.com', $conf['prod']->get('email'), 'Production email entry');
        
        $this->assertEquals('bar', $conf['stag']->get('database.host'), 'Staging database.host entry');
        $this->assertEquals('foo2', $conf['stag']->get('database.user'), 'Staging database.user entry');
        $this->assertEquals('baz2', $conf['stag']->get('database.pass'), 'Staging database.pass entry');
        $this->assertEquals('test@dev.com', $conf['stag']->get('email'), 'Staging email entry');
        
        $this->assertEquals('bar', $conf['dflt']->get('database.host'), 'Default database.host entry');
        $this->assertEquals('foo', $conf['dflt']->get('database.user'), 'Default database.user entry');
        $this->assertEquals('baz', $conf['dflt']->get('database.pass'), 'Default database.pass entry');
        $this->assertEquals('test@dev.com', $conf['dflt']->get('email'), 'Default email entry');
    }
    
    public function testGetArray() {
        $conf = $this->_newConfig('staging');
        $this->assertInternalType('array', $conf->get('database'), 'Test get array');
    }
    
    public function testSet() {
        $conf = $this->_newConfig('staging');
        
        $conf->set('database.user', 'foo123', 'Set staging database user');
        $this->assertEquals('foo123', $conf->get('database.user'), 'Check set user');
        $this->assertEquals('baz2', $conf->get('database.pass'), 'Check not-set pass');
        $this->assertEquals('bar', $conf->get('database.host'), 'Check not-set host');
        
        $conf->set('database.pass', 'foo456', 'Set staging database pass');
        $this->assertEquals('foo123', $conf->get('database.user'), 'Check previously set user');
        $this->assertEquals('foo456', $conf->get('database.pass'), 'Check just set pass');
        $this->assertEquals('bar', $conf->get('database.host'), 'Check not-set host');
    }
    
    public function testDefaultValue() {
        $conf = $this->_newConfig('production');
        
        $this->assertNull($conf->get('database.salt'), 'Get non-existant value');
        $this->assertEquals('123456', $conf->get('database.salt', '123456'), 'Get non-existant, fall back to default value');
        
        $this->assertEquals('baz1', $conf->get('database.pass'), 'Check pass');
        $this->assertEquals('baz1', $conf->get('database.pass', 'default-pass'), 'Check pass, ignoring default');
    }
}
