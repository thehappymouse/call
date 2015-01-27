<?php

class Config extends \Phalcon\Mvc\Model
{
    /**
     * @param null $parameters
     * @return Config
     */
    public static function findFirst($parameters=null){
        return parent::findFirst($parameters);
    }

    public static function setValue($key, $value)
    {
        $m = Config::findFirst("Key='" . $key . "'");
        if(null == $m){
            $m = new Config();
            $m->Key = $key;
        }
        $m->Value = $value;
        return $m->save();
    }

    /**
     * 根据 Key 获取值
     * @param $key
     * @return null|string
     */
    public static function getValue($key)
    {
        $m = Config::findFirst("Key='" . $key . "'");
        if(null == $m) return null;
        return $m->Value;
    }

    /**
     *
     * @var string
     */
    public $Key;

    /**
     *
     * @var string
     */
    public $Value;

    /**
     *
     * @var string
     */
    public $Desc;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSource('Config');
    }

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'Key' => 'Key',
            'Value' => 'Value',
            'Desc' => 'Desc'
        );
    }
}
