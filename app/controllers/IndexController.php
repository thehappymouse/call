<?php

class IndexController extends ControllerBase
{
    public function indexAction()
    {
        $this->view->name = $this->loginUser["Name"];
    }

    public function infobarAction()
    {
        //$this->view->setTemplateAfter("empty");
    }

    public function logoutAction()
    {
        $this->loginUser = null;
        $this->session->remove("auth");
        $this->redirect("index/login");
    }

    public function messageAction()
    {

    }

    public function loginAction()
    {
        $this->view->setTemplateAfter("empty");
    }

    /**
     * @param User $user
     */
    private function _registerSession($user)
    {
        $this->session->set('auth', $user->dump());
        $this->loginUser = $user->dump();
    }

    public function loginCheckAction()
    {
        $this->ajax->logData->Action = "登录";
        $name = $this->request->get("UserName");
        $pass = $this->request->get("Password");

//        if (!Dog::check()) {
//            $this->ajax->flushError("授权文件错误");
//        }


        $r = User::findFirst(array("Name=:name: AND Pass=:pass:",
            "bind" => array("name" => $name, "pass" => sha1($pass))));

        $this->ajax->logData->UserName = $name;

        if ($r) {
            $this->ajax->logData->Result = "登录成功";
            $this->ajax->logData->Data = $name;
            $this->_registerSession($r);
            $this->ajax->flushData("index");

        } else {
            $this->ajax->flushError("用户名或密码错误");
        }
    }
}

