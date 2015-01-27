<?php

class SystemLog extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    public $ID;

    /**
     *
     * @var integer
     */
    public $UserID;

    /**
     *
     * @var string
     */
    public $UserName;

    /**
     *
     * @var string
     */
    public $Action;

    /**
     *
     * @var string
     */
    public $Success;

    /**
     *
     * @var string
     */
    public $Result;

    /**
     *
     * @var string
     */
    public $Time;

    /**
     *
     * @var string
     */
    public $Data;

    /**
     *
     * @var string
     */
    public $IP;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSource('SystemLog');
    }

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'ID' => 'ID', 
            'UserID' => 'UserID', 
            'UserName' => 'UserName', 
            'Action' => 'Action', 
            'Success' => 'Success', 
            'Result' => 'Result', 
            'Time' => 'Time', 
            'Data' => 'Data', 
            'IP' => 'IP'
        );
    }

}
