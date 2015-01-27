<?php

/**
 * Created by PhpStorm.
 * 主要用于统计类查询帮助
 * User: 伟
 * Date: 14-6-24
 * Time: 下午5:07
 */
use Phalcon\Mvc\Model\Resultset\Simple;

class CountHelper extends HelperBase
{


    /**
     * 催费明细查询
     * @param array $param
     */
    public static function DetailsFee(array $param)
    {
        $p = new RequestParams($param);
        $uid = $p->get("Name"); //管理班组用户
        $data = array();

        $ss = DataUtil::getSegName($uid);

        $condition = " WHERE (a.PressTime BETWEEN :start: AND :end:) AND a.SegUser IN ($ss)";

        $query = "SELECT a.Arrear, b.Segment, a.CustomerNumber, b.ID, b.Name, b.Address, a.PressTime, a.PressStyle
                         FROM Press as a LEFT JOIN Customer as b  ON a.CustomerNumber = b.Number";


        $param = array("start" => $p->FromData, "end" => $p->ToData);

        $query .= self::addLimit($condition, (int)$p->start, $p->limit);

        $results = parent::getModelManager()->executeQuery($query, $param);

        foreach ($results as $rs) {
            $a = Arrears::findFirst($rs->Arrear);
            if ($a) {
                $rs->Money = $a->Money;
                $rs->YearMonth = $a->YearMonth;
                $rs->IsClean = $a->IsClean;
            }
            $data[] = (array)$rs;
        }

        $totalQuery = "SELECT COUNT(*) AS Count,PressStyle FROM Press AS a $condition
                              GROUP BY PressStyle ORDER By PressStyle ASC";    //电话 0 ， 通知单 1

        $td = parent::getModelManager()->executeQuery($totalQuery, $param);
        $totalData = array("Money" => 0, "Count" => 0, "Phone" => "", "Notify" => 0);
        $index = 0;
        foreach ($td as $t) {
            $totalData["Count"] += $t->Count;
            if ($index == 0) $totalData["Phone"] = $t->Count;
            else {
                $totalData["Notify"] = $t->Count;
            }
            $index++;
        }

        //TODO 原生态sql查询。其条件与普通查询条件写法不一样？
        $condition = " WHERE (a.PressTime BETWEEN :start AND :end)  AND a.SegUser IN ($ss)";
        $moneyQuery = " SELECT Sum(Money) as Money FROM Press WHERE `ID` IN (SELECT `ID` FROM Press as a  $condition GROUP BY Arrear )";

        $p = new Press();
        $md = new Simple(null, $p, $p->getReadConnection()->query($moneyQuery, $param));

        $totalData["Money"] = $md[0]->Money;

        return array($totalData, $data);
    }

    /**
     * 复电查询
     * @param array $param
     * @return array
     */
    public static function Reset(array $param)
    {
        $p = new RequestParams($param);

        $uid = $p->get("Name"); //管理班组用户

        $ss = DataUtil::getSegName($uid);


        $query = "SELECT a.CutUserName, a.Arrear,b.Segment, a.CustomerNumber, b.ID, b.Name, b.Address, a.ResetTime, a.ResetStyle, a.ResetPhone
                         FROM Cutinfo as a LEFT JOIN Customer as b ON a.CustomerNumber = b.Number
                         WHERE a.ResetTime != '' AND a.CutUserName IN ($ss)";

        //排序
        if($p->numberSort) {
            $query .= " ORDER BY CustomerNumber " . ($p->numberSort == 1 ? "Desc ": "ASC ");
        }
        if($p->segmentSort) {
            $query .= " ORDER BY b.Segment " . ($p->segmentSort == 1 ? "Desc ": "ASC ");
        }

        $param = array();
        $data = array();
        $d2 = array(); //页底的年度统计
        $results = parent::getModelManager()->executeQuery($query, $param);
        foreach ($results as $rs) {
            $a = Arrears::findFirst($rs->Arrear);
            if ($a) {
                $rs->Money = $a->Money;
                $rs->IsClean = $a->IsClean;
                $rs->YearMonth = $a->YearMonth;
            }
            $data[] = (array)$rs;

            $year = substr($rs->ResetTime, 0, 4);
            if (!isset($d2[$year])) {
                $d2[$year] = array("Year" => $year, "Count" => 0, "Money" => 0);
            }

            $d2[$year]["Count"] = (int)$d2[$year]["Count"] + 1;
            $d2[$year]["Money"] = (float)$d2[$year]["Money"] + ($a ? $rs->Money : 0);
        }


        $page =  ($p->start / $p->limit) + 1;

        $paginator = new Phalcon\Paginator\Adapter\NativeArray(
            array(
                "data" => $data,
                "limit" => $p->limit,
                "page" => $page
            )
        );
        $page = $paginator->getPaginate();

        return array($page->items, $d2, array("Count" => count($results)));

    }

    /**
     * 停电查询
     * @param array $param
     * @return array
     */
    public static function Cut(array $param)
    {
        $p = new RequestParams($param);

        $uid = $p->get("Name"); //管理班组用户
        $ss = DataUtil::getSegName($uid);

        $condition = " WHERE a.CutTime BETWEEN :start: AND :end:  AND a.CutUserName IN ($ss)";

        $query = "SELECT a.Arrear,b.Segment, a.Money, a.YearMonth, b.ID, a.CustomerNumber, b.Name, b.Address, a.CutTime, a.CutStyle, b.IsClean
                         FROM Cutinfo as a LEFT JOIN Customer as b ON a.CustomerNumber = b.Number" . $condition;


        $param = array("start" => $p->FromData, "end" => $p->ToData);

        $data = array();

        $d2 = array(); //分年度，统计其总次数

        $results = parent::getModelManager()->executeQuery($query, $param);
        foreach ($results as $rs) {

            $data[] = (array)$rs;

            $year = substr($rs->CutTime, 0, 4);
            if (!isset($d2[$year])) {
                $d2[$year] = array("Year" => $year, "Count" => 0, "Money" => 0);
            }

            $d2[$year]["Count"] = (int)$d2[$year]["Count"] + 1;
            $d2[$year]["Money"] = (float)$d2[$year]["Money"] + $rs->Money;
        }

        $page =  ($p->start / $p->limit) + 1;

        $paginator = new Phalcon\Paginator\Adapter\NativeArray(
            array(
                "data" => $data,
                "limit" => $p->limit,
                "page" => $page
            )
        );

        $page = $paginator->getPaginate();

        return array($page->items, $d2, array("Count" => count($results)));

    }

    /**
     * 电费回收明细
     * @param array $params
     */
    public static function Charges(array $params)
    {
        $p = new RequestParams($params);
        $uid = $p->get("Name");
        $start = $p->get("FromData");
        $end = $p->get("ToData");

        if (strlen($end) == strlen("2010-12-12")) {
            $end = $end . " 23:00:00";
        }

        $ss = DataUtil::getSegName($uid);
        $condition = " WHERE a.SegUser IN ($ss) AND (Time between :start: AND :end:)";

        $query = "SELECT  a.YearMonth, a.Money, a.UserName, a.Time, a.LandlordPhone, a.IsRent,
                          c.ID, a.CustomerNumber, c.Segment, c.Name, c.Address
                          FROM Charge as a inner  join Customer as c on a.CustomerNumber = c.Number";
        $query .= $condition;

        $query = parent::addLimit($query, $p->start, $p->limit);
        $results = parent::getModelManager()->executeQuery($query, array("start" => $start, "end" => $end));

        $data = array();

        foreach ($results as $r) {
            $data[] = (array)$r;
        }

        //合计信息
        $totalQuery = "SELECT COUNT(*) AS Count, SUM(Money) as Money FROM Charge AS a ";
        $totalQuery .= $condition;
        $totalData = parent::getModelManager()->executeQuery($totalQuery, array("start" => $start, "end" => $end));

        return array($totalData[0], $data);
    }

    /**
     * 对账查询
     * @param array $params
     */
    public static function AccountCheck(array $params)
    {
        $p = new RequestParams($params);
        $tid = $p->get("Team");
        $uid = $p->get("Name");
        $time = $p->get("FromData");
        $end = $p->get("ToData");
        $data = array();

        $arruid = array();
        if($tid == -1)
        {
            $users = User::find("Role=" . ROLE_TOLL);
            foreach($users as $user){
                $arruid[] = $user->ID;
            }
        }
        else if(User::IsAllUsers($uid)){
            $users = User::find("TeamID = $tid");
            foreach($users as $user){
                $arruid[] = $user->ID;
            }
        }
        else {
            $arruid[] = $uid;
        }

        $uid = "'" . implode("','", $arruid) . "'";

        $cs = Charge::find(array("UserID IN ($uid) AND (Time BETWEEN :start: AND :end:)", "bind" => array("start" => $time, "end" => $end)));

        foreach ($cs as $c) {
            $customer = Customer::findByNumber($c->CustomerNumber);
            $row = $c->dump();
            $row["IsRent"] = $customer->IsRent;
            $row["IsControl"] = $customer->IsControl;
            $row["CustomerName"] = $customer->Name;
            $data[] = $row;
        }


        $total = array();
        // UserID = 7 可更换为  ChargeTeam=3 统计这个组的
        $results = parent::getModelManager()->executeQuery("SELECT
                                                            ManageTeam,
                                                            Year,
                                                            count(if(LandlordPhone <>\"\" or RenterPhone <> \"\" ,true,null )) as PhoneCount,
                                                            SUM(Money) AS Money,
                                                            SUM(IsControl) AS ControlCount,
                                                            COUNT(ManageTeam) AS ChargeCount
                                                            FROM Charge WHERE UserID IN( $uid ) AND (Time BETWEEN '$time' and '$end')
                                                            GROUP BY ManageTeam , Year ORDER BY ManageTeam, Year");


        foreach ($results as $r) {
            $r->Team = Team::findFirst($r->ManageTeam)->Name;
            $r->Phone = "";
            $total[] = (array)$r;
        }
        return array($data, $total);
    }

    /**
     * 对账查询--》 财务
     * @param array $params
     * @return array
     */
    public static function Reconciliation(array $params)
    {
        $p = new RequestParams($params);
        $gid = (int)$p->get("Team");
        $time = $p->get("FromData");
        $end = $p->get("ToData");

        $total = array();

        $condation = " WHERE (Time BETWEEN '$time' and '$end') ";
        if($gid != -1){
            $condation .= "AND ChargeTeam = $gid";
        }

        $condation .= " GROUP BY ManageTeam, Year ORDER BY Year, ManageTeam";

        $results = parent::getModelManager()->executeQuery("SELECT
                                                            ManageTeam, ChargeTeam,
                                                            Year,
                                                            SUM(Money) AS Money,
                                                            SUM(IsControl) AS ControlCount,
                                                            COUNT(ManageTeam) AS ChargeCount
                                                            FROM Charge
                                                            $condation");
        foreach ($results as $r) {
            $r->Team = Team::findFirst($r->ManageTeam)->Name;
            $r->ChargeTeam = Team::findFirst($r->ChargeTeam)->Name;

            $total[] = (array)$r;
        }
        return $total;
    }
}