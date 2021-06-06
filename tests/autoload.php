<?php

spl_autoload_register(function($class){
    $path = substr(strrchr($class, '\\'), 1);
    if (file_exists($path . '.php')) {
        include($path . '.php');
    }
});
