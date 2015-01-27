<?php

/**
 * Created by PhpStorm.
 * User: ww
 * Date: 14-5-16
 * Time: 14:19
 */
class MainTask extends \Phalcon\CLI\Task
{
    public function mainAction()
    {
        echo "\nThis is the default task and the default action, MainTask has thsi actions \n";
//        $a =  new ControllerBase();
//        var_dump($a->getDateTime());

        $ms = get_class_methods($this);
        foreach ($ms as $m) {
            if (strstr($m, "Action")) {
                $str = str_replace("Action", "", $m);
                echo $str . "\n";
            }
        }

        echo "\nplease add args as main test param \n";
    }

    /**
     * @param array $params
     */
    public function testAction(array $params)
    {

        echo sprintf('hello %s', $params) . PHP_EOL;
    }

    /**
     * 汇总信息计算
     */
    public function allControllerAction()
    {
        $cls = array(
//            "CaptchaController",
//            "ChargesController",
//            "CountController",
//            "CountsearchController",
//            "CustomerController",
            "ExportController",
//            "ImportController",
//            "IndexController",
//            "InfoController",
//            "JpgController",
//            "ManagerController",
//            "MessageController",
//            "PoppageController",
//            "ReminderController",
//            "ReportController",
//            "ReportsearchController",
//            "SiteController",
//            "SystemController",
//            "SystemlogController",

        );

        foreach ($cls as $class) {
            $controller = strtolower(str_replace("Controller", "", $class));
            $ms = get_class_methods($class);
//
            $format = '"%s" => array(%s),';
            $data = array();
            foreach ($ms as $m) {
                if (strstr($m, "Action")) {
                    $str = str_replace("Action", "", $m);
                    $data[] = '"' . strtolower($str) . '"';
                }
            }

            $strs = implode(",", $data);
            $strs = sprintf($format, $controller, $strs);
            echo "\n" . $strs;
        }
    }

    // import
    public function advanceAction()
    {
        try {
            ExcelImportUtils::importAdvance("/opt/lampp/htdocs/ams/public/upload/1.xls");
//            ExcelImportUtils::importAdvance("/opt/lampp/htdocs/ams/app/cli/1.xls");
        } catch (PHPExcel_Reader_Exception $e) {
            echo $e->getMessage();
        }
    }

    // import arrears
    public function arrearsAction()
    {
        try {
            ExcelImportUtils::importArrears("/opt/lampp/htdocs/ams/app/cli/2.xls");
        } catch (PHPExcel_Reader_Exception $e) {
            echo $e->getMessage();
        } catch (PDOException $pe) {
            echo $pe->getMessage();
        }
    }
}