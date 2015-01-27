<?php
/**
 * Created by PhpStorm.
 * User: ww
 * Date: 14-3-12
 * Time: 16:29
 */
use Phalcon\Events\Event,
    Phalcon\Mvc\User\Plugin,
    Phalcon\Mvc\Dispatcher,
    Phalcon\Acl,
    Phalcon\Acl\Role;

class Security extends Phalcon\Mvc\User\Plugin
{

    public function __construct($dependencyInjector)
    {
        $this->_dependencyInjector = $dependencyInjector;
    }

    /**
     * 将资源添加到控制列表
     * @param $acl
     * @param $rs
     */
    private function  addAlcResource($acl, $rs)
    {
        foreach ($rs as $resource => $actions) {
            $acl->addResource(new Phalcon\Acl\Resource($resource), $actions);
        }
    }

    /**
     * 详细的控制规则
     * @param $acl
     * @param $rs
     * @param $role
     */
    private function addAclAllow($acl, $rs, $role)
    {
        foreach ($rs as $resource => $actions) {
            foreach ($actions as $action) {
                $acl->allow($role, $resource, $action);
            }
        }
    }

    public function getAcl()
    {
//        if (!isset($this->persistent->acl)) {

        $acl = new Phalcon\Acl\Adapter\Memory();

        $acl->setDefaultAction(Phalcon\Acl::DENY);

        //Register roles
        $roles = array(
            '1' => new Role('抄表员'), //抄表员
            '2' => new Role('抄表员班长'),
            '3' => new Role('收费员'),
            '4' => new Role('收费员班长'),
            '5' => new Role('对账员'),
            '6' => new Role('管理人员'),
            'guests' => new Role('Guests') //来宾，未登录
        );

        foreach ($roles as $role) {
            $acl->addRole($role);
        }

        $adminResource = array(
            "captcha" => array("index"),
            "charges" => array("charges", "withdrawn", "index", "chargeinfo", "customer", "arrears", "create", "searchfee", "cancel"),
            "count" => array("singleinquirieslist", "singleinquirieslist", "singleinquiries", "singleinquirieslist", "reconciliationinquiry", "reconciliation", "charges", "press", "cut", "reset", "customer"),
            "countsearch" => array("reconciliationinquiry", "chargecount", "reconciliationinquirylist", "reconciliation", "charges", "press", "cut", "reset", "customer"),
            "customer" => array("index", "update"),
            "export" => array("reportpress",  "press", "reportwork", "advance", "reportcharge", "accountcheck", "reconciliation", "countpress", "reset", "countcut", "countcharges", "cut", "reminder"),
            "import" => array("arrears", "advance", "editarrear", "updatemoney", "arrearslist", "progress", "advanceupload", "arrearsupload"),
            "index" => array("index", "infobar", "logout", "message", "login", "logincheck"),
            "info" => array("teamlist", "userlist", "segmentlist"),
            "jpg" => array("singlebar", "arrarscount", "cutcount", "arrarsmonth", "bar", "line", "pie"),
            "manager" => array("index", "userlist", "grouplist", "groupdel", "userdel", "build", "buildajax", "user", "group", "groupbuild", "systemlog", "changeindexline", "changepassword", "useredit", "getgroup", "groupbuildajax"),
            "message" => array("index", "list", "count", "read", "remove"),
            "poppage" => array("reminderfee", "reminder", "cancel", "powercut", "info", "userinfo", "modifyuser"),
            "reminder" => array("reminder", "powerfailure", "cancel", "restoration", "receipts", "searchfee", "searchpress", "searchreset", "cut", "cancelpress", "press", "advance", "reset"),
            "report" => array("electricity", "press", "work"),
            "reportsearch" => array("electricity", "user", "press", "teampress", "teamwork", "work", "teamwork", "teamelectricity", "user", "team",),
            "site" => array("index", "error", "index2"),
            "system" => array("index", "config"),
            "systemlog" => array("index", "list")
        );

        //抄表员
        $materResource = array(
            "site" => array("index", "index2"),
            "index" => array("message", "index"),
            "customer" => array("index", "update"),
            "poppage" => array("reminder", "powercut", "info", "userinfo", "reminderfee", "cancel"),
            "info" => array("teamlist", "userlist", "segmentlist"),
            "export" => array("reportpress", "reportwork", "advance", "reportcharge", "accountcheck", "reconciliation", "reset", "countcut", "countcharges", "cut", "reminder"),
            "count" => array("press", "cut", "reset", "customer","singleinquirieslist"),
            "countsearch" => array("reconciliationinquiry", "reconciliation", "charges", "press", "cut", "reset", "customer"),
            "jpg" => array("singlebar", "arrarscount", "cutcount", "arrarsmonth", "bar", "line", "pie"),
            "reminder" => array("reminder", "powerfailure", "cancel", "restoration", "searchfee", "searchpress", "searchreset", "cut", "cancelpress", "press", "reset"),
            "countsearch" => array("reconciliationinquiry", "reconciliation", "charges", "press", "cut", "reset", "customer"),
        );

        //抄表员班长
        $role_mater_leadResource = array_merge($materResource, array(
            "reminder" => array("reminder", "powerfailure", "cancel", "restoration", "searchfee", "searchpress",
                "searchreset", "cut", "cancelpress", "press", "advance", "reset", "receipts"),
            "report" => array("work", "electricity", "press"),
            "reportsearch" => array("work", "electricity", "press", "teamwork", "teamelectricity","user", "team",),
            "count" => array("singleinquirieslist", "singleinquirieslist", "singleinquiries", "singleinquirieslist", "reconciliationinquiry", "reconciliation", "charges", "press", "cut", "reset", "customer"),

            "export" => array("reportpress", "reportwork", "advance", "reportcharge", "accountcheck", "reconciliation", "reset", "countcut", "countcharges", "cut", "reminder"),
            "import" => array("advance", "progress", "advanceupload", "arrears", "arrearsupload"),
        ));

        //收费员
        $role_tollResource = array(
            "site" => array("index", "index2"),
            "index" => array("message", "index"),
            "poppage" => array("info", "cancel", "reminderfee", "powercut"),
            "count" => array("singleinquirieslist", "singleinquirieslist", "customer", "reconciliationinquiry", "singleinquiries"),
            "countsearch" => array("customer",  "chargecount", "reconciliationinquiry"),
            "reminder" => array("cancel", "restoration", "searchfee"),
            "info" => array("teamlist", "userlist", "segmentlist"),
            "charges" => array("charges", "arrears", "withdrawn", "index", "chargeinfo", "customer", "create", "cancel", "searchfee"),
        );

        //收费员班长
        $role_toll_leadResource = array_merge($role_tollResource, array(
            "count" => array("singleinquirieslist", "singleinquirieslist", "customer", "reconciliationinquiry", "singleinquiries"),
            "countsearch" => array("customer",  "chargecount", "reconciliationinquiry"),
            "export" => array("reportpress", "reportwork", "advance", "reportcharge", "accountcheck", "reconciliation", "reset", "countcut", "countcharges", "cut", "reminder"),
        ));

        //对账员
        $role_financeResource = array(
            "site" => array("index", "index2"),
            "index" => array("message", "index"),
            "info" => array("teamlist", "userlist", "segmentlist"),
            "poppage" => array("info", "cancel"),
            "count" => array("singleinquiries", "reconciliationinquiry", "reconciliation", "customer"),
            "countsearch" => array("reconciliationinquiry","chargecount", "chargecount", "reconciliation", "customer"),
            "export" => array("reportpress", "reportwork", "advance", "reportcharge", "accountcheck", "reconciliation", "reset", "countcut", "countcharges", "cut", "reminder"),
        );

        //管理员
        $role_managerResource = array(
            "site" => array("index", "index2"),
            "index" => array("message", "index"),
            "info" => array("teamlist", "userlist", "segmentlist"),
            "reminder" => array("cancel", "restoration", "searchfee", "searchpress", "searchreset", "advance"),
            "countsearch" => array("reconciliationinquiry", "reconciliation", "charges", "press", "cut", "reset", "customer"),
            "count" => array("singleinquiries", "reconciliationinquiry", "reconciliation", "charges", "press", "cut", "reset", "customer"),
            "export" => array("reportpress", "reportwork", "advance", "reportcharge", "accountcheck", "reconciliation", "reset", "countcut", "countcharges", "cut", "reminder"),
            "systemlog" => array("index", "list"),
            "report" => array("electricity", "press", "work"),
            "poppage" => array("reminder", "powercut", "info", "modifyuser", "cancel"),
            "reportsearch" => array("electricity", "user", "press", "work", "teamelectricity", "user", "team", "teamwork"),
            "manager" => array("index", "userlist", "grouplist", "groupdel", "userdel", "build", "buildajax", "user", "group", "groupbuild", "systemlog", "useredit", "getgroup", "groupbuildajax"),
            "import" => array("arrears", "progress", "arrearsupload"),
        );

        $publicResources = array(
            "index" => array("logout", "login", "logincheck"),
            "message" => array("count", "list", "remove"),
            "manager" => array("changepassword"),
            "countsearch" => array("reconciliationinquiry", "reconciliationinquirylist", "reconciliation", "charges", "press", "cut", "reset", "customer"),
            'site' => array('error')
        );



        $this->addAlcResource($acl, $adminResource);
//            $this->addAlcResource($acl, $publicResources);

        //应用公共权限到所有角色
        foreach ($roles as $role) {
            foreach ($publicResources as $controller => $actions) {
//                    $acl->allow($role->getName(), $resource, '*');
                $acl->allow($role->getName(), $controller, $actions);
            }
        }

        $this->addAclAllow($acl, $materResource, "抄表员");
        $this->addAclAllow($acl, $role_mater_leadResource, "抄表员班长");
        $this->addAclAllow($acl, $role_tollResource, "收费员");
        $this->addAclAllow($acl, $role_toll_leadResource, "收费员班长");
        $this->addAclAllow($acl, $role_financeResource, "对账员");
        $this->addAclAllow($acl, $role_managerResource, "管理人员");

        //The acl is stored in session, APC would be useful here too
        $this->persistent->acl = $acl;
//        }

        return $this->persistent->acl;
    }

    /**
     * This action is executed before execute any action in the application
     */
    public function beforeDispatch(Event $event, Dispatcher $dispatcher)
    {
        $auth = $this->session->get('auth');


        if (!$auth) {
            $role = 'Guests';
        } else {
            $role = $auth["Role"];
            return true;
            $role = ARole::findFirst($role)->Name;
            if ($role == "Admin") return true;
        }
        $controller = strtolower($dispatcher->getControllerName());
        $action = strtolower($dispatcher->getActionName());

        $acl = $this->getAcl();
        $allowed = $acl->isAllowed($role, $controller, $action);

        if ($allowed != Acl::ALLOW) {
            if ($role == 'Guests') {
                $dispatcher->forward(
                    array(
                        'controller' => 'index',
                        'action' => 'login'
                    )
                );
            } else {
                $this->flash->error("对不起，您没有权限访问这个模块");
                $dispatcher->forward(
                    array(
                        'controller' => 'site',
                        'action' => 'error'
                    )
                );
            }
            return false;
        }
        return true;
    }
}