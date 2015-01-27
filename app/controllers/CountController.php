<?php

class CountController extends ControllerBase
{
    public function singleInquiriesAction()
    {
        $number = $this->request->get("Number");

        if ($number != null) {
            $c = Customer::findByNumber($number);
            if (!$c) {
                $this->ajax->flushError("没有这个客户");
            }

            //查询客户的管理班组
            $user = User::findFirst(array("Name=:name:", "bind" => array("name" => $c->SegUser)));
            if ($user) {
                $team = Team::findFirst($user->TeamID);
                if ($team) {
                    $c->Team = $team->Name;
                }
            }

            //如果停电，则查询停电方式
            if ($c->IsCut) {
                $cut = Cutinfo::findFirst(array("CustomerNumber=:num:", "bind" => array("num" => $c->Number)));
                $c->CutStyle = $cut->CutStyle;
            }

            $arr = $c->Arrears;
            $list = array();

            foreach ($arr as $a) {
                $list[] = $a->dump();
            }

            $data = $c->dump(); // "List" => $list);
            $this->ajax->flushData($data);
        } else {
            //输界面
        }
    }

    public function singleInquiriesListAction()
    {
        $number = $this->request->get("Number");
        $isClean = $this->request->get("IsClean");

        if ($number != null) {
            $c = Customer::findByNumber($number);
            if (!$c) {
                $this->ajax->flushError("没有这个客户");
            }

            //查询客户的管理班组
            $user = User::findFirst(array("Name=:name:", "bind" => array("name" => $c->SegUser)));
            if ($user) {
                $team = Team::findFirst($user->TeamID);
                if ($team) {
                    $c->Team = $team->Name;
                }
            }

            //如果停电，则查询停电方式
            if ($c->IsCut) {
                $cut = Cutinfo::findFirst(array("CustomerNumber=:num: ORDER BY  CutTime Desc", "bind" => array("num" => $c->Number)));
                $c->CutStyle = $cut->CutStyle;
            }

            $arr = $c->Arrears;
            $list = array();

            foreach ($arr as $a) {
                if ($isClean == 1) {
                    if ($a->IsClean != 1) continue;
                } else {
                    if ($a->IsClean == 1) continue;
                }
                $list[] = $a->dump();
            }

            $data = $list;
            $this->ajax->flushData($data);
        } else {
            //输界面
        }
    }

    /**
     * 对账信息
     */
    public function reconciliationInquiryAction()
    {

    }

    public function reconciliationAction()
    {

    }

    public function chargesAction()
    {
        $start = $this->request->get("start");
        $limit = $this->request->get("limit");

        $data = array();
        $os = Order::find("ReceiveTime is null or ResponseTime is null limit $start,$limit");
        foreach($os as $o){
            $data[] = $o->dump();
        }

        $count = Order::count("ReceiveTime is null or ResponseTime is null");
        $this->ajax->total = $count;
        $this->ajax->flushData($data);
    }

    /**
     * 年度
     */
    public function pressAction()
    {

        $this->getData(1, $this->request);
    }

    /**
     * 季度
     */
    public function cutAction()
    {

        $this->getData(2, $this->request);
    }

    /**
     * 月度
     */
    public function resetAction()
    {
        $this->getData(0, $this->request);
    }

    /**
     * @param int $type
     * @param \Phalcon\HTTP\RequestInterface $param
     */
    private function getData($type, $param)
    {
        $key = "Monthly";
        if($type == 0){
            $key = "Monthly";
        }
        if($type == 1){
            $key = "Yearly";
        }
        if($type ==2){
            $key = "Quarterly";
        }

        $start = $param->get("FromData");
        $end = $param->get("ToData");

        $data = array();

        $builder = $this->modelsManager->createBuilder();

        $results = $builder->columns(array("Count(*) as Count", "$key as Dates", "ItemName"))
            ->from("Order")
            ->groupBy($key)
            ->groupBy("ItemName")
            ->andWhere("Yearly BETWEEN $start  AND $end ")
            ->getQuery()->execute();

        foreach ($results as $r) {
            $data[] = (array)$r;
        }

        $this->ajax->flushData($data);

    }
}