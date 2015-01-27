<?php

/**
 * Created by PhpStorm.
 * User: ww
 * Date: 14-10-31
 * Time: 16:48
 * 给extjs界面使用的报表数据查询。原辅助类继续使用，供下载
 */
class ReportNewHelper extends HelperBase
{

    private static function itemElctricity($name, $seguser, $month_arr)
    {
        $data = array(
            "Name" => $name,
            "MonthDatas" => array(),
            //用户汇总数据   应收金额，欠费金额，应收户数，欠费户数
            "AllData" => array("AllMoney" => 0, "ArrearMoney" => 0, "AllCount" => 0, "ArrearCount" => 0, "ClearMoney" => 0, "ClearCount" => 0)
        );


        foreach ($month_arr as $month) {
            $key = $month;
            $qianfei = self::getSegUserQianfei($seguser, $month);

            $b = parent::getModelManager()->createBuilder()->columns(array("SUM(Money) as Money", "SUM(House) as House"))
                ->from("Usermoney")
                ->andWhere("Month=:key:", array("key" => $key))
                ->inWhere("UserName", $seguser)
                ->getQuery();
            $results = $b->execute();
            $udata = $results[0];

            $umoney = 0;
            $uhouse = 0;
            if ($udata) {
                $umoney = $udata->Money;
                $uhouse = $udata->House;
            }

            $row = array(
                "ArrearMoney" => $qianfei->Money, //欠费余额
                "ArrearHouse" => $qianfei->Count, //欠费户数
                "uMoney" => $umoney, //应收电费
                "uHouse" => $uhouse, //应收户数
                "MoneyRate" => 0,
                "HouseRate" => 0
            );

            //欠费回收率公式：  （应收-欠费金额）/ 应收
            if ($umoney) {
                $row["MoneyRate"] = number_format(null == $umoney ? 100 : 100 * ($umoney - $qianfei->Money) / $umoney, 2);
                $row["HouseRate"] = number_format(null == $umoney ? 100 : 100 * ($uhouse - $qianfei->Count) / $uhouse, 2);
            }

            $data["AllData"]["ArrearMoney"] += $qianfei->Money;
            $data["AllData"]["AllMoney"] += $umoney;
            $data["AllData"]["ArrearCount"] +=$qianfei->Count;
            $data["AllData"]["AllCount"] += $uhouse;

            $data["MonthDatas"][$key] = $row;
        }

        if ($data["AllData"]["AllMoney"] > 0) {
            $data["AllData"]["MoneyRate"] = ($data["AllData"]["AllMoney"] - $data["AllData"]["ArrearMoney"]) * 100 / $data["AllData"]["AllMoney"];
            $data["AllData"]["MoneyRate"] = number_format($data["AllData"]["MoneyRate"], 2);
        } else {
            $data["AllData"]["MoneyRate"] = 0;
        }

        if ($data["AllData"]["AllCount"] > 0) {
            $data["AllData"]["CountRate"] = ($data["AllData"]["AllCount"]  -  $data["AllData"]["ArrearCount"]) * 100 / $data["AllData"]["AllCount"];
            $data["AllData"]["CountRate"] = number_format($data["AllData"]["CountRate"], 2);
        } else {
            $data["AllData"]["CountRate"] = 0;
        }

        return $data;
    }

    /**
     * 班组情况下的电费回收报表
     */
    public static function TeamElectricity(array $param)
    {
        $p = new RequestParams($param);
        $id = $p->get("Team");
        $start = $p->get("FromData");
        $end = $p->get("ToData");

//        if ($id == "-1") {
        $teams = Team::find("Type=1");

//        } else {
//            $teams = Team::find($id);
//        }

        $month_arr = self::getMonths($start, $end); //显示的月份

        $result = array();

        foreach ($teams as $user) {

            $seguser = DataUtil::getTeamSegNameArray($user->ID);

            $result[] = self::itemElctricity($user->Name, $seguser, $month_arr);
        }
        return $result;
    }

    public static function getMonths($start, $end)
    {

        $year1 = substr($start, 0, 4);
        $year2 = substr($end, 0, 4);

        $m1 = (int)substr($start, 4, 2);
        $m2 = (int)substr($end, 4, 2);

        $year = $year2 - $year1;
        if ($m2 > $m1) {
            $month = $m2 - $m1;
        }

        $month = $year * 12 + $month;

        $arr = array();
        for ($i = 0; $i <= $month; $i++) {
            $key = date("Ym", mktime(0, 0, 0, $m1 + $i, 1, $year1));
            $arr[] = $key;
//            $arr[$key][] = date("Y-m-d H:i:s", mktime(0, 0, 0, $m1 + $i, 1, $year1));
//            $arr[$key][] = date("Y-m-d H:i:s", mktime(23, 59, 59, $m1 + $i + 1, null, $year1));
        }

        return $arr;
    }

    //抄表员名称的 欠费金额
    private static function getSegUserQianfei($seg, $month)
    {

        $builder = parent::getModelManager()->createBuilder();
        $results = $builder->columns(array("SUM(Money) as Money", "Count( DISTINCT CustomerNumber) AS Count", "SegUser"))
            ->from("Arrears")
            ->andWhere("IsClean!=1")
            ->andWhere("YearMonth=:ym:")
            ->inWhere("SegUser", $seg)
            ->getQuery()->execute(array("ym" => $month));

        return $results->getFirst();
    }

    private static function getSegUserMonthCharge($seg, $month)
    {

        $builder = parent::getModelManager()->createBuilder();
        $results = $builder->columns(array("SUM(Money) as Money", "Count( DISTINCT CustomerNumber) AS Count", "SegUser"))
            ->from("Charge")
            ->andWhere("YearMonth=:start:")
//            ->andWhere("Time BETWEEN :start: and :end:")
            ->inWhere("SegUser", $seg)
            ->groupBy("SegUser")
            ->getQuery()->execute(array("start" => $month));
        return $results->getFirst();
    }

    /**
     * 电费回收报表、 综合排名和户回收排名
     */
    public static function Electricity(array $param)
    {
        $p = new RequestParams($param);
        $id = $p->get("Team");
        $start = $p->get("FromData");
        $end = $p->get("ToData");

        $month_arr = self::getMonths($start, $end); //显示的月份


        $result = array();

        $condation = "Role = " . ROLE_MATER;
        if ($id != -1) {
            $condation .= " AND TeamID=$id";
        }
        $users = User::find($condation);
        foreach ($users as $user) {
            $result[] = self::itemElctricity($user->Name, array($user->Name), $month_arr);
        }
        return array($result);
    }

    public static function getFeeStatementsBySeg($seg, $name, $start = null, $end = null)
    {
        $team = array();
        $years = array();

        $team["Name"] = $name;
        $team["Data"] = array();

        $allData = array("Rate" => 0, "count" => 0, "NoPressCount" => 0, "NoPressMoney" => 0); //汇总信息

        $builder = parent::getBuilder("Arrears", $seg, $start, $end);


        $results = $builder->columns(array("SUM(Money) as Money", "count(Money) as ArrearCount", "YearMonth"))
            ->groupBy("YearMonth")
            ->getQuery()->execute();

        foreach ($results as $rs) {
            $data = array("YearMonth" => $rs->YearMonth, "Rate" => 0, "NoPressCount" => $rs->ArrearCount, "NoPressMoney" => $rs->Money);

            $builder = parent::getBuilder("Arrears", $seg);
            $press = $builder->columns(array("SUM(Money) as Money", "count(Money) as Count", "YearMonth"))
                ->andWhere("PressCount > 0")
                ->andWhere("YearMonth=:ym:")
                ->groupBy("YearMonth")
                ->getQuery()->execute(array("ym" => $rs->YearMonth))->getFirst();

            if ($press) {

                $data["Rate"] = number_format((100 * $press->Count / $rs->ArrearCount), 2, ".", ""); // . "%";
                $data["NoPressCount"] = $rs->ArrearCount - $press->Count;
                $data["NoPressMoney"] = $rs->Money - $press->Money;
            }

            $team["Data"][$rs->YearMonth] = $data;
            $allData["NoPressCount"] += $data["NoPressCount"];
            $allData["NoPressMoney"] += $data["NoPressMoney"];
            $allData["count"] += $rs->ArrearCount;

            if (!in_array($rs->YearMonth, $years)) {
                $years[] = $rs->YearMonth;
            }
        }

        if ($allData["count"] > 0) {
            $allData["Rate"] = number_format((100 * ($allData["count"] - $allData["NoPressCount"]) / $allData["count"]), 2, ".", "") . "%";
            $team["AllData"] = $allData;
        }

        return array($team, $years);
    }

    /**
     * 催费报表  催费员 催费完成率    未催费户数    未催费金额
     */
    public static function Press(array $params)
    {
        $p = new RequestParams($params);

        $tid = $p->get("Team");
        if (!$tid) {
            return array(0, array());
        }

        if ($tid == -1) {
            $mTeams = Team::find("Type=1");
        } else {
            $mTeams = Team::find($tid);
        }

        $start = $p->get("FromData");
        $end = $p->get("ToData");
        $userdata = array();

        $pagestart = (int)$p->get("start");
        $index = $pagestart + 1;

        $tids = array();
        //抄表员统计情况
        foreach ($mTeams as $mt) {
            $tids[] = $mt->ID;
        }
        $tids = implode(",", $tids);

        $mUsers = User::find("TeamID IN($tids) AND Role = " . ROLE_MATER . " limit $pagestart,5");


        foreach ($mUsers as $mU) {
            $seg = DataUtil::getSegNameArray($mU->ID);

            list($uData, $a) = self::getFeeStatementsBySeg($seg, $mU->Name, $start, $end);
            $data = array("Name" => $mU->Name, "sort" => $index++);

            $allmoney = 0;
            $allcount = 0;

            if (isset($uData["Data"])) {
                foreach ($uData["Data"] as $d) {
                    $data["Action"] = "未催费金额";
                    $data["Value"] = $d["NoPressMoney"];
                    $data["YearMonth"] = $d["YearMonth"];
                    $userdata[] = $data;


                    $data["Action"] = "未催费户数";
                    $data["Value"] = $d["NoPressCount"];
                    $userdata[] = $data;

                    $data["Action"] = "催费完成率";
                    $data["Value"] = $d["Rate"];
                    $userdata[] = $data;
                }


                //添加汇总信息
                $data["YearMonth"] = "汇总";

                $data["Action"] = "未催费金额";
                $data["Value"] = $uData["AllData"]["NoPressMoney"];
                $userdata[] = $data;


                $data["Action"] = "未催费户数";
                $data["Value"] = $uData["AllData"]["NoPressCount"];
                $userdata[] = $data;


                $data["Action"] = "催费完成率";
                $data["Value"] = $uData["AllData"]["Rate"];
                $userdata[] = $data;

            }
//            else {
//                $data["Action"] = "未催费金额";
//                $data["Value"] = "";
//                $data["YearMonth"] = "";
//                $userdata[] = $data;
//
//
//                $data["Action"] = "未催费户数";
//                $userdata[] = $data;
//
//                $data["Action"] = "催费完成率";
//                $userdata[] = $data;
//
//            }
        }


        return array(User::count("TeamID IN($tids) AND Role = " . ROLE_MATER), $userdata);
    }

    private static function dayReportBySeg($seg, $start, $end)
    {
        $team = array(
            "Count" => array("Phone" => 0, "Notify" => 0, "Cut" => 0, "Reset" => 0, "Charge" => 0, "Customer" => 0),
            "Money" => array("Phone" => 0, "Notify" => 0, "Cut" => 0, "Reset" => 0, "Charge" => 0, "Customer" => 0),
        );
        //催费
        $builder = parent::getBuilder("Press", $seg);
        $builder->columns("PressStyle, COUNT(PressStyle) as Count, Sum(Money) as Money");
        $builder->groupBy("PressStyle");
        $builder->andWhere("PressTime BETWEEN '$start' AND '$end'");

        $result = $builder->getQuery()->execute();

        foreach ($result as $r) {
            if ($r->PressStyle == "电话催费") {
                $team["Count"]["Phone"] = $r->Count;
                $team["Money"]["Phone"] = $r->Money;
            } else {
                $team["Count"]["Notify"] = $r->Count;
                $team["Money"]["Notify"] = $r->Money;
            }
        }

        //停电
        $builder = parent::getBuilder("Cutinfo", $seg);
        $builder->columns("COUNT(*) as Count, Sum(Money) as Money");
        $builder->andWhere("CutTime BETWEEN '$start' AND '$end'");

        $r = $builder->getQuery()->execute()->getFirst();

        $team["Count"]["Cut"] = $r->Count;
        $team["Money"]["Cut"] = (int)$r->Money;

        //复电
        $builder = parent::getBuilder("Cutinfo", $seg);
        $builder->columns("COUNT(*) as Count, Sum(Money) as Money");
        $builder->andWhere("ResetTime BETWEEN '$start' AND '$end'");
        $builder->andWhere("ResetTime IS NOT NULL");

        $r = $builder->getQuery()->execute()->getFirst();
        $team["Count"]["Reset"] = $r->Count;
        $team["Money"]["Reset"] = (int)$r->Money;

        //收费，
        $builder = parent::getBuilder("Arrears", $seg);
        $builder->andWhere("ChargeDate BETWEEN '$start' AND '$end'");
        $builder->columns("SUM(Money) as Money, COUNT(Money) as Count,IsClean");
        $builder->andWhere("ChargeDate BETWEEN '$start' AND '$end'");
        $builder->andWhere("IsClean = 1");

        $r = $builder->getQuery()->execute()->getFirst();
        $team["Count"]["Charge"] = $r->Count;
        $team["Money"]["Charge"] = $r->Money;

        $builder = parent::getBuilder("Arrears", $seg);
        $builder->columns("SUM(Money) as Money, COUNT(Money) as Count,IsClean");
        $builder->andWhere("IsClean = 0");

        $r = $builder->getQuery()->execute()->getFirst();

        $team["Count"]["Customer"] = $r->Count;
        $team["Money"]["Customer"] = $r->Money;


        //欠费
        return $team;
    }

    /**
     * 每日工作报表
     * @param array $params
     * @return array
     */
    public static function Work(array $params)
    {

        $p = new RequestParams($params);
        $tid = $p->get("Team");

        if ($tid == "-1") {
            $mTeams = Team::find("Type=1");
        } else {
            $mTeams = Team::find($tid);
        }

        $start = $p->get("FromData");
        $end = $p->get("ToData") . "23:59:59";

        $teams = array();
        $users = array();

        //班组情况统计
        foreach ($mTeams as $mt) {

            $seg = DataUtil::getTeamSegNameArray($mt->ID);

            if (count($seg) == 0) continue;

            $team = self::dayReportBySeg($seg, $start, $end);

            $teams[$mt->Name] = $team;
        }

        //抄表员统计情况
        foreach ($mTeams as $mt) {
            $mUsers = User::find("TeamID = $mt->ID AND Role=" . ROLE_MATER);
            foreach ($mUsers as $mU) {
                $seg = DataUtil::getSegNameArray($mU->ID);
                if (count($seg) > 0) {
                    $team = self::dayReportBySeg($seg, $start, $end);
                }
                $users[$mt->Name][$mU->Name] = $team;
            }
        }
        return array($teams, $users);
    }
} 