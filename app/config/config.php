<?php

return new \Phalcon\Config(array(
    'database' => array(
        'adapter'     => 'Mysql',
        'host'        => '127.0.0.1',
        'username'    => 'power',
        'password'    => 'power_2014',
        'dbname'      => 'Call',
    ),
    'application' => array(
        'controllersDir' => __DIR__ . '/../../app/controllers/',
        'modelsDir'      => __DIR__ . '/../../app/models/',
        'viewsDir'       => __DIR__ . '/../../app/views/',
        'pluginsDir'     => __DIR__ . '/../../app/plugins/',
        'libraryDir'     => __DIR__ . '/../../app/library/',
        'apiModelDir'    => __DIR__ . '/../../api/models/',
        'cacheDir'       => __DIR__ . '/../../app/cache/',
        'baseUri'        => '/call/',
    )
));
