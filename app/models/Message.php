<?php




class Message extends \Phalcon\Mvc\Model
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
    public $ToUserID;
     
    /**
     *
     * @var integer
     */
    public $FromUserID;
     
    /**
     *
     * @var string
     */
    public $Sender;
     
    /**
     *
     * @var string
     */
    public $Content;
     
    /**
     *
     * @var string
     */
    public $SendTime;
     
    /**
     *
     * @var string
     */
    public $IsRead;
     
    /**
     *
     * @var string
     */
    public $ReadTime;
     
    /**
     *
     * @var string
     */
    public $IsImportant;
     
    /**
     *
     * @var string
     */
    public $RefCustomer;
     
    /**
     * Initialize method for model.
     */
    public function initialize()
    {
		$this->setSource('Message');

    }

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'ID' => 'ID', 
            'ToUserID' => 'ToUserID', 
            'FromUserID' => 'FromUserID', 
            'Sender' => 'Sender', 
            'Content' => 'Content', 
            'SendTime' => 'SendTime', 
            'IsRead' => 'IsRead', 
            'ReadTime' => 'ReadTime', 
            'IsImportant' => 'IsImportant', 
            'RefCustomer' => 'RefCustomer'
        );
    }

}
