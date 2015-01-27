<?php

/**
 * Created by PhpStorm.
 * excel导出使用
 * User: ww
 * Date: 14-6-24
 * Time: 10:57
 */
class ExportController extends ControllerBase
{
    public function initialize()
    {
        parent::initialize();
        $this->view->disable();
        include_once(__DIR__ . "/../../app/library/PHPExcel/Classes/PHPExcel.php");
    }

    private $headArrear = array(
        "Segment" => "抄表段编号",
        "CustomerNumber" => "用户编号",
        "CustomerName" => "用户名称",
        "Address" => "用电地址",
//        "YearMonth" => "电费年月",
        "Money" => "电费金额",
        "IsClean" => "结清标志",
        "IsCut" => "停电标志",
        "PressCount" => "催费次数",
        "CutCount" => "停电次数");

    private $headCount = array("Segment" => "抄表段编号",
        "CustomerNumber" => "用户编号",
        "Name" => "用户名称",
        "Address" => "用电地址",
        "YearMonth" => "电费年月",
        "Money" => "电费金额");

    /**
     * 拼接列，因为超过Z，使用AA,AB的形式
     * @param int $v
     */
    private function getCol($col)
    {
        if ($col > ord("Z")) {
            $j = (int)(($col - 65) / 26);
            $two = $col - 26 * $j;
            $one = ord("A") + $j - 1;

            $v = chr($one) . chr($two);

        } else {
            $v = chr($col);
        }
        return $v;
    }

    /**
     * 对账查询
     */
    public function AccountCheckAction()
    {

        list($data, $total) = CountHelper::AccountCheck($this->request->get());


        $head = array("Time" => "收费时间",
            "CustomerNumber" => "用户编号",
            "CustomerName" => "用户名称",
            "YearMonth" => "电费年月",
            "Money" => "电费金额",
            "UserName" => "收费员",
            "LandlordPhone" => "交费登记电话",
            "IsRent" => "登记租房户信息",
            "IsControl" => "签订费控");

        foreach ($data as $key => $d) {
            $d["IsControl"] = $d["IsControl"] == 1 ? "是" : "否";
            $d["IsRent"] = $d["IsRent"] == 1 ? "是" : "否";

            $data[$key] = $d;
        }
        try {

            $filename = ExcelImportUtils::arrayToExcel("对账查询", $head, $data, null, true);
            $this->ajax->flushOk("/ams/public/" . $filename);
        } catch (Exception $e) {
            $this->ajax->flushError($e->getMessage());
        }
    }

    /**
     * 对账查询（财务）
     */
    public function reconciliationAction()
    {
        $data = array();
        $os = Order::find("ReceiveTime is null or ResponseTime is null");
        foreach ($os as $o) {

            $row = $o->dump();

            if (!$o->ResponseTime) {
                $row["ResponseTime"] = "超时";
            }

            if (!$o->ResponseTime) {
                $row["ReceiveTime"] = "超时";
            }

            $data[] = $row;
        }

        $head = array_merge(array(
            "ItemName" => "操作菜单",
            "RequestTime" => "请求时间",
            "ResponseTime" => "响应时间",
            "ReceiveTime" => "接待时间",
        ));

        $floor = "共计：" . count($data) . " 次。";

        try {
            $filename = ExcelImportUtils::arrayToExcel("超时接待数据", $head, $data, $floor, true);
            $this->ajax->flushOk("/call/public/" . $filename);
        } catch (Exception $e) {
            $this->ajax->flushError($e->getMessage());
        }
    }
} 