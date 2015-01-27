<?php

class ManagerController extends ControllerBase
{
    //用户呈现页面
    public function indexAction()
    {
    }

    /**
     * 根据ID，获取角色名称
     * @param $id
     * @return string
     */
    private function getRoleName($id)
    {
        $role = ARole::findFirst($id);
        if ($role != null) {
            return $role->Name;
        } else {
            return "";
        }
    }

    /**
     * 显示用户列表
     */
    public function UserListAction()
    {

        $start = $this->request->get("start");
        $limit = $this->request->get("limit");
        $users = Menu::find("Type !=0 ORDER BY Type DESC, ID limit $start, $limit");
        $data = array();
        foreach ($users as $u) {

            $row = $u->dump();
            $menu = Menu::findFirst($u->Type);

            $row["TypeName"] = $menu->Name;
            $data[] = $row;
        }

        $this->ajax->total = Menu::count("Type !=0");
        $this->ajax->flushData($data);
    }

    /**
     * Ajax
     * 班组数据
     */
    public function GroupListAction()
    {
        $start = $this->request->get("start");
        $limit = $this->request->get("limit");
        $gs = Team::find("Type=0 limit $start, $limit");
        $data = array();
        foreach ($gs as $g) {
            $data[] = $g->dump();
        }
        $this->ajax->total = Team::count();
        $this->ajax->flushData($data);
    }

    /**
     * 删除 班组
     */
    public function GroupDelAction()
    {
        $this->ajax->logData->Action = "删除班组";
        $id = $this->request->get("ID");

        if ($id && (($u = Team::findFirst($id)) != null)) {

            $user = User::findFirst("TeamID=$id");
            if ($user) {
                $this->ajax->flushError("班组下存在用户，无法删除");
            }

            $this->ajax->logData->Data = $u->Name;
            $u->delete();

            $phql = "DELETE FROM DeviceMenu WHERE MenuID=$id";
            $this->modelsManager->executeQuery($phql);

            $this->ajax->flushOk("班组信息已删除");
        } else {
            $this->ajax->flushError("没有这个班组");
        }

    }

    /**
     * 删除用户 Ajax
     */
    public function UserDelAction()
    {
        $this->ajax->logData->Action = "删除子菜单";
        $id = $this->request->get("ID");
        if ($id && (($u = Menu::findFirst($id)) != null)) {
            $this->ajax->logData->Data = $u->Name;
            $u->delete();
            $this->ajax->flushOk("用户已删除");
        } else {
            $this->ajax->flushError("没有这个菜单");
        }
    }

    //用户创建页面
    public function buildAction()
    {

    }

    /**
     * 用户创建提交
     */
    public function BuildAjaxAction()
    {
        $this->ajax->logData->Action = "创建二级菜单";
        $name = $this->request->get("UserName");
        $this->ajax->logData->Data = $name;
        $u = new Menu();
        $u->Name = $name;
        $u->Type = $this->request->get("Group");
        $r = $u->save();

        if ($r) {
            $this->ajax->flushOk("创建成功");
        } else {
            $ms = $u->getMessages();
            $this->ajax->flushError($ms[0]->getMessage());
        }
    }


    //用户管理
    public function UserAction()
    {

    }

    //班组管理
    public function GroupAction()
    {

        $data = array();
        $ds = Device::find();
        foreach ($ds as $d) {
            $t = $d->Type == 2 ? "应答机" : "呼叫机";
            $row = array(
                "boxLabel" => $d->Number . "($t)",
                "inputValue" => $d->ID,
                "name" => $d->ID
            );
            $data[] = $row;
        }
        $this->view->boxdata = json_encode($data);
    }

    //班组创建
    public function GroupBuildAction()
    {

    }

    public function SystemlogAction()
    {

    }

    public function ChangeIndexlineAction()
    {
        $key = "IndexLine";
        $value = $this->request->get($key);
//        Config::setValue($key, $value);

        $t = Team::findFirst("ID=" . $this->loginUser["TeamID"]);
        if ($t) {
            $t->LineNumber = $value;
            $t->save();
            $this->ajax->flushOk("操作已成功");
        } else {
            $this->ajax->flushOk("班组不存在");
        }

    }

    /**
     * 修改用户密码
     */
    public function ChangePasswordAction()
    {
        $uid = $this->loginUser["ID"];
        $user = User::findFirst($uid);

        $newpass = $this->request->get("newPassWord");
        $oldpass = $this->request->get("oldPassWord");

        if ($user->Pass == sha1($oldpass)) {

            $user->Pass = sha1($newpass);
            if ($user->save()) {
                $this->ajax->flushOk("操作已成功");
            } else {
                var_dump($user->getMessages());
            }

        } else {
            $this->ajax->flushError("原始密码不正确");
        }

    }

    /**
     * 用户修改提交
     */
    public function UserEditAction()
    {
        $id = $this->request->get("ID");

        $u = Menu::findFirst($id);

        if (($g = $this->request->get("Type")) != null) {
            $u->Type = $this->request->get("Type");
        }

        $u->Name = $this->request->get("Name");

        if ($u->save()) {
            $this->ajax->flushOk("修改成功");
        } else {
            var_dump($u->getMessages());
            $this->ajax->flushError("修改失败");
        }

    }

    /**
     * 根据类型 返回不同班组
     * @param $type
     */
    public function GetGroupAction()
    {

        $Duty = $this->request->get("Duty");
        $ts = Team::find("Type=$Duty");
        $data = array();
        foreach ($ts as $a) {
            $data[] = $a->dump();
        }
        $this->ajax->flushData($data);
    }

    /**
     *  创建一级菜单
     */
    public function GroupBuildAjaxAction()
    {
        $id = $this->request->get("ID");
        if ($id) {

            $phql = "DELETE FROM DeviceMenu WHERE MenuID=$id";
            $this->modelsManager->executeQuery($phql);

            $team = Team::findFirst($id);
        } else {
            $team = new Team();
        }
        $ds = Device::find();

        $team->Name = $this->request->get("Name");

        $team->Type = 0;

        $r = $team->save();

        if ($r) {

            foreach ($ds as $d) {
                if ($this->request->get($d->ID)) {
                    $dm = new DeviceMenu();
                    $dm->MenuID = $team->ID;
                    $dm->DeviceID = $d->ID;
                    $dm->save();
                }
            }

            $this->ajax->flushOk("创建成功");
        } else {
            $ms = $team->getMessages();
            $this->ajax->flushError($ms[0]);
        }
    }
}

