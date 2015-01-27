<?php

/**
 * Created by PhpStorm.
 * User: ww
 * Date: 14-6-17
 * Time: 10:53
 */
class CustomerHelper extends HelperBase
{
    /**
     * @param $number
     */
    public static function SyncCustomerByNumber($number)
    {
        self::SyncCustomerInfo(Customer::findByNumber($number));
    }

    /**
     * @param Customer $customer
     */
    public static function SyncCustomerInfo($customer)
    {
        $money = 0;
        $count = 0;
        $presscount = 0;
        $first = "";
        $last = "";
        //$moneyQuery = "SELECT SUM(Money) AS Money, COUNT(ID) AS ArrearCount, SUM(PressCount) FROM Arrears WHERE CustomerNumber = '0268004260' AND IsClean = 0";
        $arrears = $customer->Arrears;
        $is_yushou = false;
        foreach ($arrears as $e) {
            if ($e->IsClean != 1) {
                if($e->IsClean == 2) $is_yushou = true;
                $money += (int)$e->Money;
                $count++;
                $presscount += $e->PressCount; //已结清，不计算期催费次数，欠费笔数

                if ($first == "") {
                    $first = $e->YearMonth;
                    $last = $first;
                }

                if ($e->YearMonth > $last) {
                    $last = $e->YearMonth;
                }
                if ($e->YearMonth < $first) {
                    $first = $e->YearMonth;
                }
            }
        }

        $customer->IsClean = 0;
        //如果用户没有欠费信息，则将客户IsClean=1
        if ($count == 0) {
            $customer->IsClean = 1;
        }

        if($is_yushou){
            $customer->IsClean = 2;
        }

        $customer->Money = $money;
        $customer->ArrearsCount = $count;
        $customer->PressCount = $presscount;
        $customer->LastDate = $last;
        $customer->FirstDate = $first;
        $customer->AllArrearCount = count($arrears);
        $customer->save();
    }

    /**
     * 复电
     * @param array $params
     */
    public static function Reset(array $params)
    {
        $p = new RequestParams($params);
        $result = false;

        $ids = explode(",", $p->ID);
        foreach ($ids as $id) {
            $cut = Cutinfo::findFirst(array("CustomerNumber = :num: AND ResetTime IS NULL", "bind" => array("num" => $id)));
            if ($cut == null) {
                continue;
            }

            $cut->ResetTime = $p->ResetTime;
            $cut->ResetStyle = $p->ResetType;
            $cut->ResetPhone = $p->Phone;

            $cut->ResetUser = $p->User;
            if ($cut->save()) {
//                $arrear = Arrears::findFirst($id);
//                $arrear->IsCut = 0;
//                $arrear->save();

                $customer = Customer::findByNumber($id);
                $customer->IsCut = 0;
                $customer->save();
                $result = true;
            } else {
                var_dump($cut->getMessages());
            }
        }

        return array($result, $ids);
    }

    /**
     * 停电  20140808 多记录同时停止
     * 20141027 停电和用户挂勾
     * @param array $params
     * @return bool
     */
    public static function Cut(array $params)
    {
        $success = false;
        $request = new RequestParams($params);

        $ids = explode(",", $request->ID);

        foreach ($ids as $id) {
            $cut = new Cutinfo();
            $cut->CutStyle = $request->CutType;
            $cut->CutTime = $request->CutTime;
            $cut->Arrear = $id;
//            $arrear = Arrears::findFirst($cut->Arrear);

            $customer = Customer::findFirst($id);

            $cut->CutUserName = $customer->SegUser;
            $cut->SegUser = $customer->SegUser;
            $cut->CustomerNumber = $customer->Number;
            $cut->Segment = $customer->Segment;
            $cut->Money = $customer->Money;
            $r = $cut->save();

            if ($r) {
                $customer->IsCut = 1;
                $customer->CutStyle = $cut->CutStyle;
                $customer->CutCount += 1;
                $customer->save();
                $success = true;
            } else {
                var_dump($cut->getMessages());
            }
            self::SyncCustomerInfo($customer);
        }
        return array($success, $ids);
    }

    public static function Charge(array $params)
    {
        $r = new RequestParams($params);

        $ids = $r->ID; //欠费记录
        $money = $r->Money;
        $customer = $r->Number;

        $ars = array(); //欠费信息数组

        if ($ids == null) { //id为空，表示交所有欠费，再查询一次数据库
            $ars = Arrears::find("CustomerNumber = '$customer'");
        } else {
            $ars = Arrears::find("ID IN ($ids)");
        }
    }

    //预收转逾期查询
    public static function Advances(array $param)
    {
        $p = new RequestParams($param);

        $team = $p->get("Team");
        
        $seg = DataUtil::getTeamSegNameArray($team);

        $data = array();
        if (count($seg) > 0) {
            $builder = parent::getBuilder("Customer", $seg);
            $builder->andWhere("IsClean=2");
            $builder->limit($p->get("limit"), $p->get("start"));


            $rs = $builder->getQuery()->execute();
            foreach ($rs as $r) {
                $row = $r->dump();
                $row["CustomerName"] = $row["Name"];
                $row["CustomerNumber"] = $row["Number"];
                $data[] = $row;
            }
        }

        //总计数据

        $builder = parent::getBuilder("Customer", $seg);
        $v = $builder->andWhere("IsClean=2")
                ->columns("Count(*) as Count, SUM(Balance) as Balance")->getQuery()->execute()->getFirst();
        $b = number_format($v->Balance, 2, ".", "");
        $totalInfo = array("Count" => $v->Count, "Money" => $b);

        return array($totalInfo, $data);
    }

    /**
     * 客户统计数据。增加统计信息
     * @param array $params
     */
    public static function Customers(array $params)
    {
        $countinfo = array("allCustomer" => 0, "cutCount" => 0, "specialCount" => 0);

        list($total, $data, $conditions, $param) = self::ArrearsInfo($params);
        foreach ($data as $c) {

            if ($c["IsClean"] != 1) $countinfo["allCustomer"]++;
            if ($c["IsSpecial"]) $countinfo["specialCount"]++;
            if ($c["IsCut"]) $countinfo["cutCount"]++;
        }

        return array($total, $data, $countinfo);
    }


    /**
     * 欠费用户信息，多个界面综合使用 客户分类统计中，增加统计数据
     * @param array $params
     * @return array
     */
    public static function ArrearsInfo(array $params)
    {
        $p = new RequestParams($params);
        $data = array();
        $param = array();

        $conditions = " 1=1 ";

        if ($p->CustomerNumber) {
            $conditions .= " AND Number = :Customer:";
            $param["Customer"] = $p->CustomerNumber;
        } else {
            //抄表员和抄表段
            if ($p->Number == "全部") { //选择了所有抄表段
                $str = DataUtil::getSegName($p->Name);
                $conditions .= " AND SegUser in ($str)";
            } else {
                $conditions .= " AND Segment = :segment:";
                $param["segment"] = $p->Number;
            }

            //电费年月查询
            if ($p->FromData && $p->ToData) {
                $conditions .= " AND  (FirstDate <= :end: AND LastDate >= :start:)";
                $param["start"] = $p->FromData;
                $param["end"] = $p->ToData;
            }

            //电话有效   ['全部', '2'],['是', '1'],['否', '0']PhoneEffective
            if ($p->PhoneEffective != null && $p->PhoneEffective != 2) {
                if ($p->PhoneEffective == 1) {
                    $conditions .= ' AND (LandlordPhone !=""  OR RenterPhone != "")';
                } else {
                    $conditions .= ' AND (LandlordPhone = "" AND RenterPhone ="")';
                }
            }
            //是否停电
            if ($p->PowerCutLogo != null && $p->PowerCutLogo != 2) {
                $conditions .= " AND IsCut = :IsCut:";
                $param["IsCut"] = $p->PowerCutLogo;
            }

            if ($p->CutType != null && $p->CutType !=2) {

                $conditions .= " AND CutStyle = :cutstyle:";
                $param["cutstyle"] = $p->CutType;
            }

            //是否特殊客户
            if ($p->IsSpecial != NULL && $p->IsSpecial != 2) {
                $conditions .= " AND IsSpecial = :IsSpecial:";
                $param["IsSpecial"] = $p->IsSpecial;
            }

            //是否结清
            if ($p->IsClean != NULL && $p->IsClean != 2) {
                if(3 == $p->IsClane) {
                    $conditions .= " AND (IsClean = 1 OR IsClean = 2)";
                }
                else {
                    $conditions .= " AND IsClean = :IsClean:";
                    $param["IsClean"] = $p->IsClean;
                }
            }


            //欠费金额
            if ($p->ArrearsValue && $p->ArrearsValue > 0) {

                if ($p->Arrears == 1) {
                    $word = " >= ";
                } else {
                    $work = " < ";
                }
                $conditions .= " AND Money $word :money:";
                $param["money"] = $p->ArrearsValue;
            }
            //催费次数
            if ($p->ReminderFeeValue && (int)$p->ReminderFeeValue > 0) {
                if ($p->ReminderFee == 1) $word = ">=";
                else $word = "<";
                $conditions .= " AND PressCount $word :PressCount:";
                $param["PressCount"] = $p->ReminderFeeValue;
            }

            //欠费次数
            if ($p->ArrearsItemsValue && (int)$p->ArrearsItemsValue > 0) {
                if ($p->ArrearsItems == 1) $word = ">=";
                else $word = "<";
                $conditions .= " AND ArrearsCount $word :ArrearsCount:";
                $param["ArrearsCount"] = $p->ArrearsItemsValue;
            }

            //停电次数
            if ($p->PowerOutagesValue && (int)$p->PowerOutagesValue > 0) {
                $word = ($p->PowerOutages == 1) ? ">=" : "<";

                $conditions .= " AND CutCount $word :CutCount:";
                $param["CutCount"] = $p->PowerOutagesValue;
            }

            //费控标志
            if ($p->IsControl != null && $p->IsControl != 2) {
                $conditions .= " AND IsControl = :IsControl:";
                $param["IsControl"] = $p->IsControl;
            }

            //是否租房  2 全部。 1 租。0 非租
            if ($p->IsRent != null && $p->IsRent != 2) {
                $conditions .= " AND IsRent = :IsRent:";
                $param["IsRent"] = $p->IsRent;
            }
        }

        $conditions_mini = $conditions;
        $conditions .= " limit $p->start, $p->limit";

        $results = Customer::find(array($conditions, "bind" => $param));
        foreach ($results as $rs) {
            $d = $rs->dump();
            $d["CustomerName"] = $rs->Name;
            $d["CustomerNumber"] = $rs->Number;
            $data[] = $d;
        }

        $total = Customer::count(array($conditions_mini, "bind" => $param));
        return array($total, $data, $conditions_mini, $param);
    }
} 