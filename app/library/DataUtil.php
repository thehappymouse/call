<?php

/**
 * Created by PhpStorm. 各种复杂的元数组查询
 * User: ww
 * Date: 14-6-13
 * Time: 09:50
 */
class DataUtil
{

    /**
     * @param \Phalcon\Mvc\Model $model
     */
    public  static function getModelError($model)
    {
        $msgs = $model->getMessages();
        if(count($msgs) > 0){
            return $msgs[0]->getMessage();
        }
        else{
            return "";
        }
    }

    /**
     * 数组形式获取用户名称。
     * @param $uid
     * @return array
     */
    public static function getSegNameArray($uid)
    {
        if(($tid = User::IsAllUsers($uid))){
            if($tid == -1){
                $users = User::find("Role=" . ROLE_MATER);
            }
            else {
                $users = User::find("TeamID=$tid AND Role=" . ROLE_MATER);
            }
        }
        else {
            $users = User::find("ID=$uid");
        }

        $names = array();
        foreach($users as $u){
            $names[] = $u->Name;
        }
        return $names;
    }

    public  static function getTeamSegNameArray($tid)
    {
        if($tid == -1){
            $users = User::find("Role=" . ROLE_MATER);
        }
        else {
            $users = User::find("TeamID=$tid AND Role=" . ROLE_MATER);
        }

        $names = array();
        foreach($users as $u){
            $names[] = $u->Name;
        }
        return $names;
    }

    /**
     * 查询字符串形式返回用户名称
     * @param $uid  uid  tid开头，表示全部  tid_1 表示1班全部  tid_-1表示所有班组
     * @return string
     */
    public static function getSegName($uid)
    {
        $names = self::getSegNameArray($uid);
        $ss = "'" . implode("','", $names) . "'";
        return $ss;
    }

    /**
     * 获取 管理员名下的抄表段，仅抄表段
     * @param $uid
     * @return array
     */
    public static function GetSegmentsByUid($uid)
    {
        $data = array();
        $ss = Segment::find("UserID=$uid ORDER BY Number");
        foreach ($ss as $s) {
            $data[] = $s->Number;
        }
        return $data;
    }


    /**
     * 获取 管理员名下的抄表段集合
     * @param $uid
     * @return array
     */
    public static function GetSegmentsDataByUid($uid)
    {
        $data = array();
        $ss = Segment::find("UserID=$uid ORDER BY Number");
        foreach ($ss as $s) {
            $data[] = $s->dump();
        }
        return $data;
    }

    /**
     * 查找管理班组用户的抄表段集合, 并以sql拼接
     * 2014-08-08 检查 uid， tid_1 表示组下所有用户
     * @param $uid
     * @return string
     */
    public static  function GetSegmentsStrByUid($uid)
    {
        if(($tid = User::IsAllUsers($uid))){

            $ss = DataUtil::GetSegmentsStrByTid($tid);
        }
        else {
            $ss = self::GetSegmentsByUid($uid);
            $ss = "'" . implode("','", $ss) . "'";
        }
        return $ss;
    }

    /**
     * 查询一个抄表班下所有抄表段，拼接给sql查询
     * @param $tid
     * @return array|string
     */
    public static function GetSegmentsStrByTid($tid){
        $segs = self::GetSegmengsByTid($tid);

        $ss = "'" . implode("','", $segs) . "'";
        return $ss;
    }



    /**
     * 取班组下所有抄表段数据集合
     * @param $tid
     * @return array
     */
    public static function GetSegmentDataByTid($tid)
    {
        $data = array();
        $sql = "SELECT DISTINCT * from SegmentLog WHERE UserID in (SELECT ID FROM User WHERE TeamID = ?) ORDER BY Number";
        $param = array($tid);


        $segment = new Segment();

        $rs = new Phalcon\Mvc\Model\Resultset\Simple(null, $segment, $segment->getReadConnection()->query($sql, $param));
        foreach($rs as $r){
            $data[] = $r->dump();
        }

        return $data;
    }

    /**
     * 获取 班组 下，所有的抄表段
     * @param $tid
     * @return array
     */
    public static function GetSegmengsByTid($tid)
    {
        $data = array();
        $sql = "SELECT DISTINCT * from Segment WHERE UserID in (SELECT ID FROM User WHERE TeamID = ?)";
        $param = array($tid);


        $segment = new Segment();

        $rs = new Phalcon\Mvc\Model\Resultset\Simple(null, $segment, $segment->getReadConnection()->query($sql, $param));
        foreach($rs as $r){
            $data[] = $r->Number;
        }

        return $data;
    }

} 