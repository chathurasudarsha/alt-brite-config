Brite Config
============

A simple PHP INI (or plain array) configuration class with dot-notation access
------------------------------------------------------------------------------

* Parses both INI files and PHP arrays
* Deals allows for configuration inheritance
* Available via [Composer / Packagist](https://packagist.org/packages/donsimon/alt-brite-config)

Usage
-----

You need a configuration file. Example `.ini` contents:

    [default]
    
    database.host = bar
    database.user = foo
    database.pass = baz
    
    service.api_key = 123456
    
    email = test@dev.com
    
    [staging:default]
    
    database.user = foo2
    database.pass = baz2
    
    [production:staging]
    
    database.user = foo1
    database.pass = baz1
    email = test@production.com


Or alternatively, if you prefer plain PHP arrays:

```php
<?php

$config['default']['database']['host'] = 'bar';
$config['default']['database']['user'] = 'foo';
$config['default']['database']['pass'] = 'baz';

$config['default']['service']['api_key'] = '123456';
$config['default']['email'] = 'test@dev.com';

$config['production']['extends'] = 'staging';
$config['production']['database']['user'] = 'foo1';
$config['production']['database']['pass'] = 'baz1';
$config['production']['email'] = 'test@production.com';

$config['staging']['extends'] = 'default';
$config['staging']['database']['user'] = 'foo2';
$config['staging']['database']['pass'] = 'baz2';
```


If you want to use the 'registry', register your configuration file during bootstrap:

```php
<?php

use Brite\Config\Config;

// Registering as the 'default' config means you can grab the config
// without specifying a name.
Config::register('default', __DIR__ . '/test_config/config.php', 'staging');
```

Then access your configuration when required:

```php
<?php

use Brite\Config\Config;

// This grabs the config registered as 'default'
$config = Config::instance();

echo $config->get('database.host');
// output: "bar"
echo $config->get('database.user');
// output: "foo1"
```

Alternatively, if you have multiple configuration files, you may name your
configuration something other than 'default' during bootstrap, and access it
via:

```php
<?php

use Brite\Config\Config;

// This grabs the config registered as 'database'
$dbConfig = Config::instance('database');

echo $dbConfig->get('host');
echo $dbConfig->get('user');
```

However, we all know that global access is bad, right? You can create your
own instance to contain your configuration, rather than using a global static 
registry:

```php
<?php

use Brite\Config\IniConfig;

$config = new IniConfig('/path/to/file.ini', 'staging');

// Now register $config with your registry
```

... and that's it. Simple!
