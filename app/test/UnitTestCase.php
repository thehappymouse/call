<?php
use Phalcon\DI,
    \Phalcon\Test\UnitTestCase as PhalconTestCase,
    \Phalcon\Test\ModelTestCase as PhalconModelTestCase;

ini_set('date.timezone','Asia/Shanghai');
abstract class UnitTestCase extends PhalconModelTestCase {

    /**
     * @var \Voice\Cache
     */
    protected $_cache;

    /**
     * @var \Phalcon\Config
     */
    protected $_config;

    /**
     * @var Phalcon\Mvc\Model\Manager
     */
    protected $modelsManager;

    /**
     * @var bool
     */
    private $_loaded = false;

    public function setUp(Phalcon\DiInterface $di = NULL, Phalcon\Config $config = NULL) {

        // Load any additional services that might be required during testing
        $di = DI::getDefault();

        // get any DI components here. If you have a config, be sure to pass it to the parent
        parent::setUp($di);

        $this->_loaded = true;
        if ($di->get("modelsManager")) {
            $this->modelsManager = $di->get("modelsManager");
        }
    }

    /**
     * Check if the test case is setup properly
     * @throws \PHPUnit_Framework_IncompleteTestError;
     */
    public function __destruct() {
        if(!$this->_loaded) {
            throw new \PHPUnit_Framework_IncompleteTestError('Please run parent::setUp().');
        }
    }

    public function out($msg)
    {
        echo date("Y-m-d h:i:s\t") . $msg . "\n";
    }
}
