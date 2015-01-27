<?php

/**
 * 将原本写在Controller中的处理逻辑方法向下移动，规属于Model层，以便进行测试
 * User: ww
 * Date: 14-6-17
 * Time: 10:54
 */
class HelperBase
{
    /**
     * @return datetime 2000-01-01 12:00:00
     */
    public static function getDateTime()
    {
        return date("Y-m-d H:i:s");
    }

    /**
     * 获取日期 2000-01-01
     */
    public static function getDate($time = null)
    {
        if (!$time) $time = time();
        return date("Y-m-d H:i:s", $time);
    }

    /**
     * @return Phalcon\Mvc\Model\Manager
     */
    protected static function getModelManager()
    {
        return \Phalcon\DI::getDefault()->get("modelsManager");
    }


    /**
     * 将sql语句查询条件后，增加分页条件
     * @param string $condation
     * @param $page
     * @param $pageSize
     * @return string
     */
    public  static function addLimit($condition, $start = 0, $limit = 30)
    {
//        $size = $pageSize ? $pageSize : 30;
//
//        $page = $page ? $page : 1;
//        $start = ($page - 1) * $size;
        if($limit == null) $limit = 30;

        $condition .= " LIMIT $start, $limit";

        return $condition;
    }

    /**
     * 表查询
     * @param $table
     * @throws Phalcon\Http\Client\Exception
     * @return \Phalcon\Mvc\Model\Query\BuilderInterface
     */
    protected static function getBuilder($table, $seg = null, $start = null, $end = null)
    {
        $manager = self::getModelManager();
        if ($manager == null) {
            throw new \Phalcon\Http\Client\Exception("modelsManager is not set, please check the config file");
        }

        $builder = $manager->createBuilder();
        $builder->from($table);

        if ($seg) {
            $builder->inWhere("SegUser", $seg);
        }
        if ($start && $end) {

            $builder->andWhere("YearMonth BETWEEN '$start' AND '$end'");
        }

        return $builder;
    }
} 