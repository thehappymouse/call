<?php




class DeviceMenu extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    public $DeviceID;
     
    /**
     *
     * @var string
     */
    public $MenuID;
     
    /**
     *
     * @var integer
     */
    public $ID;
     
    /**
     * Initialize method for model.
     */
    public function initialize()
    {
		$this->setSource('DeviceMenu');

    }

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'DeviceID' => 'DeviceID', 
            'MenuID' => 'MenuID', 
            'ID' => 'ID'
        );
    }

}
