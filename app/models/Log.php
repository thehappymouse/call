<?php




class Log extends \Phalcon\Mvc\Model
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
    public $DeviceID;
     
    /**
     *
     * @var integer
     */
    public $ItemID;
     
    /**
     *
     * @var string
     */
    public $ItemName;
     
    /**
     *
     * @var string
     */
    public $Action;
     
    /**
     *
     * @var string
     */
    public $ActionTime;
     
    /**
     * Initialize method for model.
     */
    public function initialize()
    {
		$this->setSource('Log');

    }

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'ID' => 'ID', 
            'DeviceID' => 'DeviceID', 
            'ItemID' => 'ItemID', 
            'ItemName' => 'ItemName', 
            'Action' => 'Action', 
            'ActionTime' => 'ActionTime'
        );
    }

}
