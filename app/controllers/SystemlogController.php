<?php

class SystemlogController extends ControllerBase
{

    public function indexAction()
    {

    }

    public function listAction()
    {
        $limit = (int)$this->request->get("limit");
        $start = (int)$this->request->get("start");

        $end = $this->request->get("ToData");
        if(strlen($end) < strlen("2000-12-12 01:00:00")) {
            $end = $end . " 23:59:59";
        }

        $robots = Systemlog::find(array(
            "Time BETWEEN :start: AND :end: Order By Time Desc limit $start,$limit",
            "bind" => array("start" => $this->request->get("FromData"), "end" => $end)));


        //$as = Systemlog::find("1=1 Order By Time Desc");
        $data = array();
        foreach ($robots as $s) {
            $data[] = $s->dump();
        }

        $this->ajax->total = Systemlog::count(array(
            "Time BETWEEN :start: AND :end: Order By Time Desc",
            "bind" => array("start" => $this->request->get("FromData"), "end" => $end)));
        $this->ajax->flushData($data);
    }
}

