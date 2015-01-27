<?php

define("CMD", "/root/test/kkk ");
//狗不存在
//进程挂掉
//是否过期
class Dog
{
    public static function run($cmd, &$val = 0)
    {
        ob_start();
        $ret = system($cmd, $val);
        ob_end_clean();

        return $ret;
    }

    public static function get_current()
    {
        return self::run(CMD . " -t");
    }

    public static function get_limit()
    {
        return self::run(CMD . " -l");
    }

    public static function set_current($current)
    {
        return self::run(CMD . " -c " . $current);
    }

    public static function set_limit($limit)
    {
        return self::run(CMD . " -k " . $limit);
    }

    public static function check()
    {
        $current = self::get_current();
        $limit = self::get_limit();
        $c = time();
        if ($c < $current) {
            return false;
        } else if ($c > $limit) {
            return false;
        } else {
            self::set_current($c);
            return true;
        }
    }
}
	
