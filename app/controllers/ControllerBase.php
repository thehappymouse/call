<?php

use Phalcon\Mvc\Controller;

define("ROLE_MATER", 1); //抄表员
define("ROLE_MATER_LEAD", 2); //抄表员班长
define("ROLE_TOLL", 3); //收费员
define("ROLE_TOLL_LEAD", 4); //收费员班长
define("ROLE_FINANCE", 5); //财务，对账
define("ROLE_MANAGER", 6); //领导
define("ROLE_ADMIN", 7); //admin

class ControllerBase extends Controller
{
    /**
     * @var Array
     */
    protected $loginUser;

    /**
     * @var Customer
     */
    protected $customer;

    /**
     * @var CustomerID
     */
    protected $CustomerID;

    /**
     * @var AjaxR
     */
    protected $ajax;

    public function initialize()
    {
        $this->view->setTemplateAfter("main");
        $this->loginUser = $this->session->get('auth');
        $user = $this->loginUser;

        $this->ajax = new AjaxR($user);
    }

    protected function forward($uri)
    {
        $uriParts = explode('/', $uri);

        return $this->dispatcher->forward(
            array(
                'controller' => $uriParts[0],
                'action' => $uriParts[1]
            )
        );
    }

    /**
     * @return datetime 2000-01-01 12:00:00
     */
    public function getDateTime()
    {
        return date("Y-m-d H:i:s");
    }

    public function getDateOnly($time = null){

        if (!$time) $time = time();
        return date("Y-m-d", $time);
    }

    /**
     * 获取日期 2000-01-01
     */
    public function getDate($time = null)
    {
        if (!$time) $time = time();
        return date("Y-m-d H:i:s", $time);
    }

    protected function redirect($uri)
    {
        return $this->response->redirect($uri);
    }


    /**
     * @param string $table
     * @param array $seg
     * @param null $start
     * @param null $end
     * @return \Phalcon\Mvc\Model\Query\BuilderInterface
     */
    protected function getBuilder($table, array $seg = null, $start = null, $end = null)
    {
        $builder = $this->modelsManager->createBuilder();
        $builder->from($table);
        if ($seg) {
            $builder->inWhere("SegUser", $seg);
        }
        if ($start && $end) {
            $builder->andWhere("YearMonth BETWEEN '$start' AND '$end'");
        }
        return $builder;
    }
}
