<?php
    $di = new \Phalcon\DI\FactoryDefault();

    //Set up the database service
    $di->set('db', function()
    {
        return new \Phalcon\Db\Adapter\Pdo\Mysql(array
        (
            "host"      => "localhost",
            "username"  => "power",
            "password"  => "power_2014",
            "dbname"    => "Call",
            "options" => array
            (
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
            )
        ));
    });

    $di->set('modelsManager', function()
    {
        return new Phalcon\Mvc\Model\Manager();
    });

    $app = new Phalcon\Mvc\Micro();
    $app->setDI($di);

    $loader = new \Phalcon\Loader();
    $loader->registerDirs(array
    (
        __DIR__ . '/models/', 
        str_replace('api', 'app', __DIR__) . '/models/', 
        __DIR__ . '/utils/'
    ))->register();

    //Retrieves all menu
    $app->get('/v1/menu', function() use ($app)
    {
        $did = $app->request->get('did');
        if (empty($did)) $did = 1;

        $menu = MenuManager::getMenu($did);

        echo json_encode($menu);
    });

    //Retrieves menu based on primary key
    /*
    $app->get('/v1/menu/{id:[0-9]+}', function($id) 
    {
        $menus = array();

        $menu = Menu::find(array('order' => 'Type ASC'));
    });
    */

    //Updates menu based on primary key
    $app->get('/v1/menu/{id:[0-9]+}', function($btn) use ($app)
    {
        $act = $app->request->get('act');
        $dno = $app->request->get('did');

        $device = MenuManager::getDeivce($dno);
        if (null == $device) return ;

        $did = $device->Type;
        $id = $did . '_' . $btn;

        $state = StateManager::changeState($btn, $act);
        _log($did, $btn, $state);
    });

    $app->get('/v1/all', function()
    {
        var_dump(CacheManager::getInstance()->getAll());
    });

    $app->handle();


    function _log($did, $bid, $state)
    {
        $menu = MenuManager::getCachedMenu('btns');
        if (!empty($menu))
        {
            $ms = unserialize($menu);

            $button = $ms[$bid];
            list($msg, $btn, $beep, $flash) = MenuManager::getStates($did, $state - 1);

            $log = new Log();
            $log->DeviceID = $did;
            $log->ItemID = $bid;
            $log->ItemName = $button;
            $log->Action = $btn;
            $log->ActionTime = date('Y-m-d H:i:s');
            $log->save();
        }
    }
?>
