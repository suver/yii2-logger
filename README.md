log target for Yii2
============================

Credits
-------
Benjamin Zikarsky https://github.com/bzikarsky/gelf-php

Installation
------------
The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require "suver/yii2-logger" "*"
```

or add

```json
"suver/yii2-logger" : "*"
```

to the `require` section of your application's `composer.json` file.

Usage
-----

Add Graylog target to your log component config:
```php
<?php
return [
    ...
    'components' => [
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                'file' => [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
                'graylog' => [
                    'class' => 'suver\logger\GraylogTarget',
                    'levels' => ['error', 'warning', 'info'],
                    'categories' => ['application'],
                    'logVars' => [], // This prevent yii2-debug from crashing ;)
                    'transport' => [
                        'type' => 'tcp', // udp, amqp
                        'host' => 'graylog',
                        'port' => '12201',
                    ],
                    'facility' => 'facility-name',
                ],
            ],
        ],
    ],
    ...
];
```



```php
<?php
// short_message will contain string representation of ['test1' => 123, 'test2' => 456],
// no full_message will be sent
Yii::info([
    'test1' => 123,
    'test2' => 456,
]);

// short_message will contain 'Test short message',
// two additional fields will be sent,
// full_message will contain all other stuff without 'short' and 'add':
// string representation of ['test1' => 123, 'test2' => 456]
Yii::info([
    'test1' => 123,
    'test2' => 456,
    'short' => 'Test short message',
    'add' => [
        'additional1' => 'abc',
        'additional2' => 'def',
    ],
]);

// short_message will contain 'Test short message',
// two additional fields will be sent,
// full_message will contain 'Test full message', all other stuff will be lost
Yii::info([
    'test1' => 123,
    'test2' => 456,
    'short' => 'Test short message',
    'full' => 'Test full message',
    'add' => [
        'additional1' => 'abc',
        'additional2' => 'def',
    ],
]);
```
