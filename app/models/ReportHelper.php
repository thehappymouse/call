<?php

class ReportHelper extends HelperBase
{


    /**
     * 获取抄表段集合下的欠费总户数，总金额，欠费年月
     * @param array $seg
     * @return mixed
     */
    private static function getMonthArrears(array $seg)
    {
        $builder = parent::getModelManager()->createBuilder();
        $results = $builder->columns(array("SUM(Money) as Money", "count(Money) as ArrearCount", "YearMonth"))
            ->from("Arrears")
            ->inWhere("SegUser", $seg)
            ->groupBy("YearMonth")
            ->getQuery()->execute();
        return $results;
    }

    /**
     * 电费回收报表
     */
    public static function Electricity(array $param)
    {

        $p = new RequestParams($param);
        $id = $p->get("Team");

        $result = array();
        $years = array();

        if ($id == -1) {
            $sql = "SELECT  * from User WHERE Role = " . ROLE_MATER . " AND TeamID in (SELECT ID FROM Team WHERE Type = 1)";
            $u = new User();
            $users = new Phalcon\Mvc\Model\Resultset\Simple(null, $u, $u->getReadConnection()->query($sql));
        } else {
            $users = User::find("TeamID=$id AND Role = " . ROLE_MATER);
        }



        foreach ($users as $user) {

            $oneRes = array("User" => $user->Name);

            $seg = DataUtil::getSegNameArray($user->ID);
            if (count($seg) == 0) continue;
            $results = self::getMonthArrears($seg);

            $data = array();

            //汇总信息
            $allData = array("Money" => 0, "NoChargeMoney" => 0, "Rate" => 0, "ArrearCount" => 0, "NoChargeCount" => 0, "CountRate" => 0);

            foreach ($results as $rs) {

                $b = parent::getBuilder("Charge", $seg)
                    ->columns(array("SUM(Money) as Money", "count(Money) as ChargeCount"))
                    ->andWhere("YearMonth=:ym:")->getQuery()->execute(array("ym" => $rs->YearMonth));

                $rb = $b->getFirst();

                $rs->NoChargeMoney = $rs->Money - ($rb->Money ? $rb->Money : 0);
                $rate = number_format(100 * ($rb->Money ? $rb->Money : 0) / $rs->Money, 2, '.', '');
                $rs->Rate = $rate . "%"; //费用回报率

                $rs->NoChargeCount = $rs->ArrearCount - ($rb->ChargeCount);
                //户数统计
                $rs->CountRate = number_format(100 * $rb->ChargeCount / $rs->ArrearCount, 2, ".", "") . "%";

                $data[$rs->YearMonth] = (array)$rs;

                if (!in_array($rs->YearMonth, $years)) {
                    $years[] = $rs->YearMonth;
                }

                $allData["Money"] += $rs->Money;
                $allData["NoChargeMoney"] += $rs->NoChargeMoney;

                $allData["ArrearCount"] += $rs->ArrearCount;
                $allData["NoChargeCount"] += $rs->NoChargeCount;
            }
            if (count($results) > 0){
                $allData["Rate"] = number_format(100 - (100 * $allData["NoChargeMoney"] / $allData["Money"]), 2, ".", "") . "%";
                $allData["CountRate"] = number_format(100 - (100 * $allData["NoChargeCount"] / $allData["ArrearCount"]), 2, ".", "") . "%";
            }

            $oneRes["Data"] = $data;
            $oneRes["AllData"] = $allData;
            $result[] = $oneRes;
        }
        //根所汇总信息，进行一次排序
        sort($years);

        return array($years, $result);
    }

    private static function getFeeStatementsBySeg($seg, $name, $start = null, $end = null)
    {
        $team = array();
        $years = array();

        $team["Name"] = $name;
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

                $data["Rate"] = number_format((100 * $press->Count / $rs->ArrearCount), 2, ".", "") . "%";
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
     * 催费报表
     */
    public static function Press(array $params)
    {
        $p = new RequestParams($params);

        $tid = $p->get("Team");

        if ($tid == -1) {
            $mTeams = Team::find("Type=1");
        } else {
            $mTeams = Team::find($tid);
        }

        $start = $p->get("FromData");
        $end = $p->get("ToData");

        $teams = array();
        $years = array();
        $users = array();
        $ty = array();

        //班组情况统计
        foreach ($mTeams as $mt) {

            $seg = DataUtil::getTeamSegNameArray($mt->ID);

            list($team, $years) = self::getFeeStatementsBySeg($seg, $mt->Name, $start, $end);
            foreach($years as $t){
                if(!in_array($t, $ty)){
                    $ty[] = $t;
                }
            }


            $teams[] = $team;
        }

        $years = $ty;

        //抄表员统计情况
        foreach ($mTeams as $mt) {
            $mUsers = User::find("TeamID = $mt->ID");
            foreach ($mUsers as $mU) {
                $seg = DataUtil::getSegNameArray($mU->ID);
                if (count($seg) > 0) {
                    list($uData, $a) = self::getFeeStatementsBySeg($seg, $mU->Name, $start, $end);
                    $users[] = $uData;
                }
            }
        }
        sort($years);
        return array($years, $teams, $users);
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
                $seg = DataUtil::GetSegmentsByUid($mU->ID);
                if (count($seg) > 0) {
                    $team = self::dayReportBySeg($seg, $start, $end);
                }
                $users[$mt->Name][$mU->Name] = $team;
            }
        }
        return array($teams, $users);
    }
} 