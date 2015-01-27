<?php

function flag(&$cache, $step, $id, $time) {
	if ($cache[$id]["step"] == $step - 1) {
		$cache[$id]["step$step"] = $time;
		$cache[$id]["step"] =  $step;
	} else {
		$cache[$id]["step$step"] = 0;
		save($cache, $id);
		$cache[$id]["step"] = 1;
		$cache[$id]["step$step"] = $time;
	}

	if ($cache[$id]["step"] == 3) {
		save($cache, $id);
		$cache[$id]["step"] = 0;
	}
}

$menu_name_cache = array();
function getMenuName($id) {
	global $menu_name_cache;
	if (!array_key_exists($id, $menu_name_cache)) {
		$pid = getMenuId($id);
		$menu = Menu::find("ID = $id");
		$menu_name_cache[$id] = $menu[0]->Name;
	}

	return $menu_name_cache[$id];
}

$menu_id_cache = array();
function getMenuId($id) {
	global $menu_id_cache;
	if (!array_key_exists($id, $menu_id_cache)) {
		$menu = Menu::find("Type = $id");
		$menu_id_cache[$id] = $menu[0]->Type;
	}
	return $menu_id_cache[$id];
}

function save(&$cache, $id) {
	$order = new Order();
	$order->MenuID   = getMenuId($id);
	$order->MenuName = getMenuName($id);
	$order->ItemName = $cache[$id]['Name'];

	$step =  $cache[$id]['step'];
	$order->RequestTime = $cache[$id]['step1'];

	if ($step >= 2) {
		$order->ResponseTime = $cache[$id]['step2'];
	}

	if ($step >= 3) {
		$order->ReceiveTime = $cache[$id]['step3'];
	}

	$order->Yearly = date('Y');
	$order->Monthly = date('Y-m');
	$order->Quarterly = toDaLiReadQuarter(date('Y'), ceil(date('n') / 3));
	$order->save();

}


function toDaLiReadQuarter($year, $number) {
	$map = array("",  "一", "二", "三" , "四");
	return $year . " 第" . $map[$number] . "季度";
}


class LogTask extends \Phalcon\CLI\Task
{
    public function calcAction()
    {
    	$config = Config::find("Key = 'request'");
    	$timeout_call = $config[0]->Value;

    	$config = Config::find("Key = 'response'");
    	$timeout_wait = $config[0]->Value;

    	$cache = array();
    	foreach (Log::find() as $log) {

    		$id   = $log->ItemID;
    		$time = $log->ActionTime;


    		if (!array_key_exists($id, $cache)) {
    			$cache[$id] = array("step" => 0);
    			$cache[$id]["Name"] = $log->ItemName;
    		}

    		if ($log->Action == "呼叫") {
    			flag($cache, 1, $id, $time);
    		} else if ($log->Action == "确认") {
    			flag($cache, 2, $id, $time);
    		} else if ($log->Action == "空闲") {
    			flag($cache, 3, $id, $time);
    		} else {
    			var_dump($log->Action); 
    			print "unkown action";
    		}

    		$log->delete();
    	}

    	foreach($cache as $id => $value) {
    		if ($value["step"] == 3) {
    			continue;
    		} else {
    			save($cache, $id);
    		}
    	}

    }
}