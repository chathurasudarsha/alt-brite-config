<?php

require_once 'autoload.php';
require_once 'PhpConfigTest.php';

use Brite\Config\PhpConfig,
    Brite\Config\ArrayConfig,
    Brite\Config\Config;

class IniConfigTest extends PhpConfigTest {
    protected function _newConfig($section) {
        return new \Brite\Config\IniConfig(__DIR__ . '/test_config/config.ini', $section);
    }
}
