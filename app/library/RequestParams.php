<?php

/**
 * 主要用来封装一个get方法，效果如果 request->get, 不存在的键，直接返回null
 * User: ww
 * Date: 14-6-17
 * Time: 13:46
 */
class RequestParams
{
    private  $data;

    /**
     * @param  array $data
     */
    public function  __construct($data)
    {
        $this->data = $data;
        if(!$this->PageSize) $this->PageSize = 30;
        if(!$this->Page) $this->Page=0;
    }

    public function setData($data)
    {
        $this->data = $data;
    }

    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

    public function __get($name)
    {
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }
        return null;
    }

    public function get($key)
    {
        return $this->$key;
    }
} 