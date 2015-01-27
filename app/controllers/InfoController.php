<?php


class InfoController extends ControllerBase
{
    var $role;

    public function initialize()
    {
        $this->view->disable();

        parent::initialize();

        $this->role = $this->loginUser["Role"];
    }

    /**
     * 班组信息
     */
    public function  TeamListAction()
    {
        $arr = array();
        $teams = Menu::find("Type=0");
        foreach ($teams as $team) {
            $arr[] = $team->dump();
        }

        $this->ajax->flushData($arr);
    }

    /**
     * 根据TeamID，返回用户信息
     */
    public function UserListAction()
    {
        $tid = $this->request->get("ID");


        if ($this->role == ROLE_MATER) { //抄表员 只能查看自己
            $users = User::find("ID=" . $this->loginUser["ID"]);
        } else if ($this->role == ROLE_MATER_LEAD) {    //班长, 添加全部字段
            $users = User::find(array("Role = 1 AND TeamID = :TID:", "bind" => array("TID" => $tid)));
        } else {
            $users = User::find(array("(Role = 1 OR Role = 3) AND TeamID = :TID:", "bind" => array("TID" => $tid)));
        }

        if($this->role != ROLE_MATER){
            $arr[] = array("ID" => "tid_$tid", "Name" => "全部");
        }

        foreach ($users as $u) {
            $arr[] = $u->dump();
        }

        $this->ajax->flushData($arr);
    }


    /**
     * 根据用户ID，得到该用户的抄表段
     */
    public function SegmentListAction()
    {

        $all = array("ID" => "全部", "Name" => "全部");
        $arr[] = $all;
        $teams = Menu::find("Type=" . $this->request->get("Type"));
        foreach ($teams as $team) {
            $arr[] = $team->dump();
        }


        $this->ajax->flushData($arr);
    }
}

