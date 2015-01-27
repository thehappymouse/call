<?php




class Order extends \Phalcon\Mvc\Model
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
    public $MenuID;
     
    /**
     *
     * @var string
     */
    public $MenuName;
     
    /**
     *
     * @var string
     */
    public $ItemName;
     
    /**
     *
     * @var string
     */
    public $RequestTime;
     
    /**
     *
     * @var string
     */
    public $ResponseTime;
     
    /**
     *
     * @var string
     */
    public $ReceiveTime;
     
    /**
     *
     * @var integer
     */
    public $Yearly;
     
    /**
     *
     * @var integer
     */
    public $Monthly;
     
    /**
     *
     * @var string
     */
    public $Quarterly;
     
    /**
     * Initialize method for model.
     */
    public function initialize()
    {
		$this->setSource('Order');

    }

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'ID' => 'ID', 
            'MenuID' => 'MenuID', 
            'MenuName' => 'MenuName', 
            'ItemName' => 'ItemName', 
            'RequestTime' => 'RequestTime', 
            'ResponseTime' => 'ResponseTime', 
            'ReceiveTime' => 'ReceiveTime', 
            'Yearly' => 'Yearly', 
            'Monthly' => 'Monthly', 
            'Quarterly' => 'Quarterly'
        );
    }

}
