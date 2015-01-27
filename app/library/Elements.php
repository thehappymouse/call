<?php

/**
 * Elements
 *
 * Helps to build UI elements for the application
 */
class Elements extends Phalcon\Mvc\User\Component
{
    private $_headerMenu = array();

    //弹出页面的面包屑导航
    private $PopPage = array(
        'reminder' => array(
            'controller' => 'PopPage',
            'action' => 'reminder',
            'caption' => '催费次数'
        ),
        'PowerCut' => array(
            'controller' => 'PopPage',
            'action' => 'PowerCut',
            'caption' => '停电次数'
        ),
        'Info' => array(
            'controller' => 'PopPage',
            'action' => 'Info',
            'caption' => '用户信息'
        ),
        'modify' => array(
            'controller' => 'PopPage',
            'action' => 'modify',
            'caption' => '用户修改'
        )
    );

    private function getMenuByRole($role)
    {
        $role = ARole::findFirst($role);

        $ms = Module::find("ID IN ($role->Modules) ORDER BY Sort");

        $first = array();

        $ams = Module::find("ParentID=-1");
        foreach ($ams as $a) {
            $row = $a->dump();
            $row["model"] = array();
            $first[$a->ID] = $row;
        }

        foreach ($ms as $m) {
            if (-1 != $m->ParentID) {
                $first[$m->ParentID]["model"][] = $m->dump();
            }
        }

        return $first;
    }

    public function getPermissionsMenu()
    {
        $auth = $this->session->get('auth');
//        $auth["Role"] = 7;;
        if ($auth) {
            $moels = $this->getMenuByRole($auth["Role"]);

            $headerMenu = $moels;
        } else {
            $headerMenu = array();
        }

        return $headerMenu;
    }

    public function getSubMenu()
    {
        $auth = $this->session->get('auth');
        $controllerName = strtolower($this->view->getControllerName());
        $actionName = $this->view->getActionName();

        $li_base = "<LI><A class='' id=mainMenu_item_1_1%s title=管理 onclick=javascript:mainMenu.setTab(%s) href='javascript:void(0)' target=_self>%s</A></LI>";
        $moels = $this->getPermissionsMenu();
        $index = 1;
        foreach ($moels as $m) {
            if (count($m["model"]) == 0) continue;
            $display = ($index == 1) ? "block" : "none";
            echo '<UL id="mainMenu_menu_' . $index . '" style="display:' . $display . '">';

            $second = $m["model"];
            $j = 1;
            foreach ($second as $se) {
                $action =  $se["Url"];
                echo '    <LI><A id="mainMenu_item_' . $index . '_'. $j++ .'" target=PageFrame href="' . $action . '" title="收费">' . $se["Name"] . '</A></LI>';
            }
            echo '</UL>';

            $index++;
        }

    }

    public function getMenu()
    {
        $auth = $this->session->get('auth');
        $controllerName = strtolower($this->view->getControllerName());
        $actionName = $this->view->getActionName();

        $class = "";
        $li_base = '<LI id=mainMenu_tab_%s><A class="%s"  title=%s  href="javascript:void(0)" >%s</A></LI>';
        $moels = $this->getPermissionsMenu();
        $index = 1;
        foreach ($moels as $m) {

            if (count($m["model"]) == 0) continue;

            if ($index == 1) {
                $class = "active";
            } else {
                $class = "";
            }

            echo sprintf($li_base, $index, $class, $m["Name"], $m["Name"]);

            $index++;
        }


    }

    /**
     * Builds header menu with left and right items
     *
     * @return string
     */
    public function getMenu2()
    {
        $auth = $this->session->get('auth');
        $controllerName = strtolower($this->view->getControllerName());
        $actionName = $this->view->getActionName();


        echo '<ul class="nav nav-tabs" >';
        $moels = $this->getPermissionsMenu();

        if ($auth["Role"] == ROLE_MATER) {
            echo '<li><a href="/ams/site/index">首页</a></li>';
        }
        foreach ($moels as $m) {

            if (count($m["model"]) == 0) continue;

            if ($controllerName == $m["Url"]) {
                echo '<li class="active dropdown">';

            } else {
                echo '<li class="dropdown">';
            }

            echo '<a class="dropdown-toggle" data-toggle="dropdown" href="#">' . $m["Name"] . '<span class="caret"></span> </a>';
            echo '<ul class="dropdown-menu" role="menu">';

            $second = $m["model"];

            foreach ($second as $se) {
                if ($se["Name"] == "divider") {
                    echo '<li class="divider"></li>';
                } else {
                    $action = '/ams/' . $se["Url"];
                    echo '<li><a href="' . $action . '">' . $se["Name"] . '</a></li>';
                }
            }
            echo '</ul>';
            echo '</li>';
        }

        echo '<li style="float:right"><a href="/ams/index/logout">退出</a></li>';
        echo '<li style="float:right" id="message"><a href="/ams/index/message">消息</a></li>';
        echo '<li style="float:right"><a >' . $auth['Name'] . '</a></li>';

        echo '</ul>';
    }

    /*
    *   获取面包屑导航
    */
    public function getBreadcrumbs()
    {

        $headerMenu = $this->getPermissionsMenu();
        $controllerName = $this->view->getControllerName();
        $actionName = $this->view->getActionName();

        if ($controllerName == "PopPage") {
            $headerMenu = $this->PopPage;
            foreach ($headerMenu as $menu) {
                if ($actionName == $menu['action']) {
                    echo '<ul class="breadcrumb Breadcrumbs"><li>当前位置<span class="divider">/</span></li>';
                    echo '<li class="active">' . $menu['caption'] . '</li>';
                    echo '</ul><div class="Horizontal"><div>';
                }
            }

        } else {
            $m = Module::findFirst("Url='$controllerName'");
            $m2 = Module::findFirst("Url='$controllerName/$actionName'");

            echo '<ul class="breadcrumb Breadcrumbs"><li>当前位置<span class="divider">/</span></li>';

            if ($m)
                echo '<li>' . $m->Name . '<span class="divider">/</span></li>';
            if ($m2)
                echo '<li class="active">' . $m2->Name . '</li>';

            echo '</ul><div class="Horizontal"><div>';
        }
    }
}
