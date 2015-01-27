<?php
    /**
     *  StateManager
     *  @author chenxi
     */
    final class StateManager
    {
        const PROCESS_TIMEOUT = 20;
        public static function changeState($id, $act)
        {
            $state = 0;
            if (CacheManager::getInstance()->exists($id))
            {
                $state = CacheManager::getInstance()->get($id);
            }
            switch ($act)
            {
                case 'addon' :
                    $state += 1;

                    if (4 <= $state) $state = 0;
                    break;
                case 'reduce' :
                    $state -= 1;

                    if (0 > $state) $state = 0;
                case 'clear' : 
                    $state = 0;
                    break ;
                default :
                    break ;
            }

            CacheManager::getInstance()->set($id, $state, StateManager::PROCESS_TIMEOUT);

            return $state;
        }

        public static function getState($id)
        {
            $state = 0;
            if (CacheManager::getInstance()->exists($id))
            {
                $state = CacheManager::getInstance()->get($id);
            }

            return $state;
        }
    }
?>