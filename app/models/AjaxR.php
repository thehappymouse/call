<?php
/**
 * 为Ajax返回数据辅助类
 * User: 伟
 * Date: 14-5-21
 * Time: 下午5:28
 * 14-6-17 增加操作日志入库
 */

ini_set('date.timezone', 'Asia/Shanghai');

class AjaxR
{
    var $success = true;
    var $data = array();
    var $msg = "";
    var $code = 0;
    var $total = 0;

    /**
     * 用于记录操作日志
     * @var SystemLog
     */
    var $logData = null;

    public function  __construct($user = null)
    {
        $this->logData = new SystemLog();
        $this->logData->IP = $_SERVER["REMOTE_ADDR"];
        $this->logData->Time = HelperBase::getDateTime();
        if ($user != null) {
            $this->logData->UserID = $user["ID"];
            $this->logData->UserName = $user["Name"];
        }
    }

    public function flushOk($msg = "")
    {
        $this->logData->Result = "操作成功";
        $this->success = true;
        $this->msg = $msg;
        $this->flush();
    }

    public  function flush()
    {
        $this->flushLog($this->logData);
        unset($this->logData);
        echo json_encode($this);
        exit;
    }

    /**
     * 日志入库
     * @param SystemLog $log
     */
    public function flushLog($log)
    {
        if ($log->Action != "" && $log->Data != "") {
            //$r = $log->save();
        }
    }

    public function flushData($data)
    {
        if (null == $data || 0 == count($data)) {
            $this->success = false;
            $this->msg = "没有数据";
        }
        $this->data = $data;
        $this->flush();
    }

    public function flushError($msg)
    {
        $this->logData->Result = $msg;

        $this->success = false;
        $this->msg = $msg;
        $this->flush();
    }
}