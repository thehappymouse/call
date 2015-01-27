<?php
    //error_reporting(0);
    final class MenuManager
    {
        public static function getMenu($dno)
        {
            $menus = array();

            $device = MenuManager::getDeivce($dno);
            if (null == $device) return ;

            $did = $device->ID;
            $type = $device->Type;
            $ids = MenuManager::getType($did);
            $menu = Menu::find(array("ID IN ($ids)", 'order' => 'Type ASC, ID ASC'));

            $btns = array();
            foreach ($menu as $m)
            {
                $btns[$m->ID] = $m->Name;
                if (0 == $m->Type)
                {
                    $menus[$m->ID] = array('ID' => $m->ID, 'Name' => $m->Name);
                }
                else
                {
                    $id = $did . '_' . $m->ID;
                    $state = StateManager::getState($m->ID);
                    $menus[$m->Type][] = array
                    (
                        'ID' => $m->ID, 
                        'Name' => $m->Name, 
                        'State' => $state, 
                        'Action' => ''
                    );
                }
            }

            $ms = array();
            foreach ($menus as $m)
            {
                $arr = array();
                //var_dump($m);
                $arr['name'] = @$m['Name'];
                $arr['id'] = @$m['ID'];
                $arr['list'] = array();
                foreach ($m as $o)
                {
                    if (is_array($o))
                    {
                        list($msg, $btn, $beep, $flash) = MenuManager::getStates($type, $o['State']);
                        $arr['list'][] = array
                        (
                            'id' => $o['ID'], 
                            'menu' => $o['Name'], 
                            'msg' => $msg, 
                            'state'=> $o['State'], 
                            'button' => $btn, 
                            'beep' => $beep,
                            'flash' => $flash,
                            'action' => MenuManager::getAction($type, $o['State'])
                        );
                    }
                }

                $ms[] = $arr;
            }

            MenuManager::cache($menu);
            MenuManager::cache($btns, 'btns');
           
            return $ms;
        }

        public static function getCachedMenu($name='menu')
        {
            return CacheManager::getInstance()->get($name);
        }

        private static function cache($data, $key='menu')
        {
            $s = serialize($data);
            $md5 = md5($s);

            $c = CacheManager::getInstance()->get($key);
            if (!empty($c))
            {
                $m = CacheManager::getInstance()->get($key . '_md5');

                if ($m == $md5) return ;
            }

            CacheManager::getInstance()->set($key, serialize($data));
            CacheManager::getInstance()->set($key . '_md5', $md5);
        }

        public static function getDeivce($dno)
        {
            $device = Device::findFirst("Number = '$dno'");

            return $device;
        }

        private static function getType($did)
        {
            $ids = array();

            $dv = DeviceMenu::find('DeviceID = ' . $did);
            foreach ($dv as $d)
            {
                $ids[] = $d->MenuID;
            }
            $id = join(',', $ids);

            $phql = "SELECT ID FROM Menu WHERE Type IN ($id)";
            $ms = \Phalcon\DI::getDefault()->get("modelsManager")->executeQuery($phql);

            $mids = array();
            foreach ($ms as $m)
            {
                $mids[] = $m->ID;
            }

            $tmp = array_merge($ids, $mids);
            
            sort($tmp);

            return join(',', $tmp);
        }

        public static function getStates($type, $state)
        {
            static $frontBtn = array('-1' => '空闲', 0 => '呼叫', 1 => '取消', 2 => '确认', 3 => '异常');
            static $backendBtn = array('-1' => '等待', 0 => '等待', 1 => '确认', 2 => '等待', 3 => '异常');

            static $frontMsg = array(0 => '请按呼叫按钮呼叫业务人员', 1 => '请按取消按钮停止呼叫', 2 => '请按确认按钮结束', 3 => '异常');
            static $backendMsg = array(0 => ' ', 1 => '您有来自前端的呼叫请求，请确认', 2 => '您已确认', 3 => '异常');

            static $frontFlash = array(0 => 'false', 1 => 'true', 2 => 'true', 3 => 'true');
            static $backFlash = array(0 => 'false', 1 => 'true', 2 => 'false', 3 => 'true');

            switch ($type)
            {
                case 1:
                    return array($frontMsg[$state], $frontBtn[$state], 'false', $frontFlash[$state]);
                default :
                    return array($backendMsg[$state], $backendBtn[$state], (1 == $state) ? 'true': 'false', $backFlash[$state]);
            }
        }

        public static function getAction($did, $state)
        {
            //return (1 == $state) ? 'reduce': 'addon';

            switch ($did)
            {
                case 1:
                    if (0 == $state) return 'addon';
                    else if (1 == $state) return 'reduce';
                    else return 'clear';
                default :
                    if (1 == $state) return 'addon';
                    else return '';
            }
        }
    }
?>
