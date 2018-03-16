<?php
return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'session' => [
            'class' => 'yii\web\CacheSession',  // use cache component
            'timeout' => 3600*3,
        ],
        'cache' => [
            'class' => 'yii\redis\Cache',   // use redis component
        ],
        'redis' => [
            'class' => 'yii\redis\Connection',
            'hostname' => '127.0.0.1',
            'port' => 6379,
            'database' => 0,
        ],
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=120.27.236.17;dbname=baodan',
            'username' => 'baodan',
            'password' => 'ySWlGET9pwes',
            'charset' => 'utf8',
            'tablePrefix' => 'dk_',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
    ],
];
