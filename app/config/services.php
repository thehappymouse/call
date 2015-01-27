<?php

use Phalcon\DI\FactoryDefault;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Url as UrlResolver;
use Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter;
use Phalcon\Mvc\View\Engine\Volt as VoltEngine;
use Phalcon\Mvc\Model\Metadata\Memory as MetaDataAdapter;
use Phalcon\Session\Adapter\Files as SessionAdapter;

use Phalcon\Logger,
    Phalcon\Db\Adapter\Pdo\Mysql as Connection,
    Phalcon\Events\Manager;

/**
 * The FactoryDefault Dependency Injector automatically register the right services providing a full stack framework
 */
$di = new FactoryDefault();

/**
 * The URL component is used to generate all kind of urls in the application
 */
$di->set('url', function () use ($config) {
    $url = new UrlResolver();
    $url->setBaseUri($config->application->baseUri);

    return $url;
}, true);

/**
 * 用户登录验证
 */
$di->set('dispatcher', function() use ($di) {
    //Obtain the standard eventsManager from the DI
    $eventsManager = $di->getShared('eventsManager');

    //Instantiate the Security plugin
    $security = new Security($di);

    //Listen for events produced in the dispatcher using the Security plugin
    $eventsManager->attach('dispatch', $security);

    $dispatcher = new Phalcon\Mvc\Dispatcher();

    //Bind the EventsManager to the Dispatcher
    $dispatcher->setEventsManager($eventsManager);

    return $dispatcher;
});

$di->set("elements", function() use ($di){
    return new Elements();
});


$di->set('modelsCache', function(){

    //Cache data for one day by default
    $frontCache = new Phalcon\Cache\Frontend\Data(array(
        "lifetime" => 86400
    ));

    //Memcached connection settings
    $cache = new Phalcon\Cache\Backend\Memcached($frontCache, array(
        "host" => "localhost",
        "port" => "11211"
    ));

    return $cache;
});


/**
 * Setting up the view component
 */
$di->set('view', function () use ($config) {

    $view = new View();

    $view->setViewsDir($config->application->viewsDir);

    $view->registerEngines(array(
        '.volt' => function ($view, $di) use ($config) {

            $volt = new VoltEngine($view, $di);

            $volt->setOptions(array(
                'compiledPath' => $config->application->cacheDir,
                'compiledSeparator' => '_',
                'compileAlways' => true
            ));

            return $volt;
        },
        '.html' => 'Phalcon\Mvc\View\Engine\Php',
    ));

    return $view;
}, true);


/**
 * Register the flash service with custom CSS classes
 */
$di->set('flash', function(){
    return new Phalcon\Flash\Direct(array(
        'error' => 'alert alert-danger',
        'success' => 'alert alert-success',
        'notice' => 'alert alert-info',
    ));
});

/**
 * Database connection is created based in the parameters defined in the configuration file
 */
$di->set('db', function () use ($config) {


    $eventsManager = new \Phalcon\Events\Manager();
    $logger = new \Phalcon\Logger\Adapter\File("../app/logs/debug.log");
    $eventsManager->attach('db', function($event, $connection) use ($logger) {
        if ($event->getType() == 'beforeQuery') {
//            $logger->log($connection->getSQLStatement(), Logger::INFO);
        }
    });

    $db =  new DbAdapter(array(
        'host' => $config->database->host,
        'username' => $config->database->username,
        'password' => $config->database->password,
        'dbname' => $config->database->dbname,
        "options" => array(
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
        )
    ));

    $db->setEventsManager($eventsManager);
    return $db;
});

$di->set('modelsManager', function(){
    return new Phalcon\Mvc\Model\Manager();
});

/**
 * If the configuration specify the use of metadata adapter use it or use memory otherwise
 */
$di->set('modelsMetadata', function () {
    return new MetaDataAdapter();
});

/**
 * Start the session the first time some component request the session service
 */
$di->set('session', function () {
    $session = new SessionAdapter();
    $session->start();
    return $session;
});
