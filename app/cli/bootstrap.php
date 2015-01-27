<?php
use Phalcon\DI,
    Phalcon\DI\FactoryDefault;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Url as UrlResolver;
use Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter;
use Phalcon\Mvc\View\Engine\Volt as VoltEngine;
use Phalcon\Mvc\Model\Metadata\Memory as MetaDataAdapter;
use Phalcon\Session\Adapter\Files as SessionAdapter;

use Phalcon\DI\FactoryDefault\CLI as CliDI;
use Phalcon\CLI\Console as ConsoleApp;

define('VERSION', '1.0.0');

//Using the CLI factory default services container
$di = new CliDI();
// Define path to application directory
defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(__FILE__)));

/**
 * Register the autoloader and tell it to register the tasks directory
 */
$loader = new \Phalcon\Loader();
$loader->registerDirs(
    array(
        APPLICATION_PATH . '/tasks',
        __DIR__ . '/../../app/models/',
        __DIR__ . '/../../app/controllers/',
        __DIR__ . '/../../app/library/',
        __DIR__ . '/../../app/library/PHPExcel/Classes/',
    )
);

$loader->register();

$config = new \Phalcon\Config(array(
    'database' => array(
        'adapter' => 'Mysql',
        'host' => '192.168.1.5',
        'username' => 'power',
        'password' => 'power_2014',
        'dbname' => 'Call',
    ),
));

$di->set('db', function () use ($config) {
    return new DbAdapter(array(
        'host' => $config->database->host,
        'username' => $config->database->username,
        'password' => $config->database->password,
        'dbname' => $config->database->dbname,
        "options" => array(
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
        )
    ));
});

$di->set('modelsManager', function(){
    return new Phalcon\Mvc\Model\Manager();
});

//Create a console application
$console = new ConsoleApp();
$console->setDI($di);


/**
 * Process the console arguments
 */
$arguments = array();
$params = array();
if ($argv && is_array($argv)) {
    foreach ($argv as $k => $arg) {
        if ($k == 1) {
            $arguments['task'] = $arg;
        } elseif ($k == 2) {
            $arguments['action'] = $arg;
        } elseif ($k >= 3) {
            $params[] = $arg;
        }
    }
    if (count($params) > 0) {
        $arguments['params'] = $params;
    }
}


// define global constants for the current task and action
define('CURRENT_TASK', (isset($argv[1]) ? $argv[1] : null));
define('CURRENT_ACTION', (isset($argv[2]) ? $argv[2] : null));


try {
    // handle incoming arguments
    $console->handle($arguments);
} catch (\Phalcon\Exception $e) {
    echo $e->getMessage();
    exit(255);
}