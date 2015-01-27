<?php
    /**
     * CacheManager
     * @author chenxi
     */
    final class CacheManager
    {
        private $_cache;
        private function __construct()
        {
            //$frontCache = new Phalcon\Cache\Frontend\Data(array('lifetime' => 30));

            $this->_cache = new Memcached;
            $this->_cache->addServer('127.0.0.1', 11211);
        }

        public static function getInstance()
        {
            static $_instance = null;
            if (null == $_instance)
            {
                $_instance = new CacheManager();
            }

            return $_instance;
        }

        public function get($key)
        {
            return $this->_cache->get($key);
        }

        public function set($key, $val, $life=0)
        {
            return $this->_cache->set($key, $val, $life);
        }

        public function exists($key)
        {
            $val = $this->_cache->get($key);
            
            return is_numeric($val);
        }

        public function delete($key)
        {
            return $this->_cache->delete($key);
        }

        public function getAll()
        {
            $keys = $this->_cache->getAllKeys();
            
            $all = array();
            foreach ($keys as $key)
            {
                if (is_numeric($key) || 'menu' == $key || 'menu_md5' == $key)
                    $all[$key] = $this->_cache->get($key);
            }

            return $all;
        }
    }
?>