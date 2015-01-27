<?php

class UserController extends ControllerBase
{

    public function moneyAction()
    {

    }

    public function moneyUpdateAction()
    {
        $money = $this->request->get("Money");
        $date = $this->request->get("Month");
        $uid = $this->request->get("Name");
        $house = $this->request->get("House");

        $users = DataUtil::getSegNameArray($uid);
        $users = "'" . implode("','", $users) . "'";
        $users = User::find("Name IN ($users)");

        foreach ($users as $user) {

            $um = Usermoney::findFirst(array("UserID=:uid: AND Month=:date:",
                "bind" => array("uid" => $user->ID, "date" => $date)));
            if($um){

            }
            else {
                $um = new Usermoney();
                $um->Month = $date;
                $um->UserID = $user->ID;
                $um->UserName = $user->Name;
                $um->House = $house;
            }
            $um->Money = $money;
            $um->House = $house;
            if (!$um->save()) {
                $this->ajax->flushError(DataUtil::getModelError($um));
            }
        }

        $this->ajax->flushOk("操作已成功");
    }

    public function listAction()
    {
        $tid = $this->request->get("Team");
        $uid = $this->request->get("Name");
        $fd = $this->request->get("FromData");
        $ed = $this->request->get("ToData");
        $start = $this->request->get("start");
        $limit = $this->request->get("limit");

        $users = DataUtil::getSegNameArray($uid);
        $users = "'" . implode("','", $users) . "'";

        $param = array("start" => $fd, "end" => $ed);
        $count = Usermoney::count(array("UserName IN ($users) AND (Month BETWEEN:start: AND :end:)", "bind" => $param));
        $this->ajax->total = $count;

        $ums = Usermoney::find(array("UserName IN ($users) AND (Month BETWEEN:start: AND :end:) limit $start , $limit",
            "bind" => $param));
        $data = array();
        foreach ($ums as $um) {
            $row = $um->dump();
            $data[] = $row;
        }


        $this->ajax->flushData($data);
    }
}

