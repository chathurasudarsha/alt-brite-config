<?php

// Deliberately an unordered jumble!

$config['default']['database']['host'] = 'bar';
$config['default']['database']['user'] = 'foo';
$config['default']['database']['pass'] = 'baz';

$config['production']['extends'] = 'staging';
$config['production']['database']['user'] = 'foo1';
$config['production']['database']['pass'] = 'baz1';

$config['staging']['extends'] = 'default';
$config['staging']['database']['user'] = 'foo2';
$config['staging']['database']['pass'] = 'baz2';

$config['default']['service']['api_key'] = '123456';

$config['default']['email'] = 'test@dev.com';
$config['production']['email'] = 'test@production.com';
