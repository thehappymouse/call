<?php

/**
 * Class  测试Controller的方法
 */
class ControllerTest extends \UnitTestCase
{

    public function testRequestParm()
    {
        $this->out("Test RequestParam Class ------------");

        $params["Team"] = "1";
        $params["Number"] = "QC5SC113";
        $params["FromData"] = "2014-06-10";
        $params["ToData"] = "2014-06-17";
        $params["Arrears"] = "1";
        $params["Team"] = "1";
        $params["Page"] = "1";
        $params["PageSize"] = "30";

        $p = new RequestParams($params);

        $this->assertEquals($p->Team, "1");
        $this->assertNull($p->Test);
        $this->assertEquals($p->Number, "QC5SC113");
        $this->assertNull($p->get("NullAtt"));

    }

    public function testCut()
    {
        $this->out("----" . __FUNCTION__ . "----");

        $params = array();
        $params["ID"] = 21;
        $params["CutType"] = "远";
        $params["CutTime"] = HelperBase::getDate();

        $count = Cutinfo::count("Arrear=" . $params["ID"]);
        var_dump($count);
        list($r, $data) = CustomerHelper::Cut($params);
        $count2 = Cutinfo::count("Arrear=" . $params["ID"]);
        if($r){
            $this->assertEquals($count + 1, $count2);
        }

    }

    /**
     * 用户欠费信息 获取
     * 保证方法在做出修改后，测试通过，表示可以正常返回
     */
    public function testCustomerArrears()
    {
        $this->out("----" . __FUNCTION__ . "----");

        $params = array();

        list($total, $data) = CustomerHelper::ArrearsInfo($params);
        $this->assertTrue(count($data) == 30);
        $this->assertTrue(count($data) <= 30);

        $params["Number"] = "QC5SD152";
        list($total, $data) = CustomerHelper::ArrearsInfo($params);


        $params["Team"] = "1";
        $params["Number"] = "QC5SC113";
        $params["FromData"] = "2014-06-10";
        $params["ToData"] = "2014-06-17";
        $params["Arrears"] = "1";
        $params["Team"] = "1";
        $params["Page"] = "1";
        $params["PageSize"] = "1";

        list($total, $data) = CustomerHelper::ArrearsInfo($params);

        $this->assertTrue(count($data) <= 1);

    }
}
