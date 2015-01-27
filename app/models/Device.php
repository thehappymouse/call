<?php




class Device extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    public $ID;
     
    /**
     *
     * @var string
     */
    public $Number;
     
    /**
     *
     * @var string
     */
    public $Type;
     
    /**
     * Initialize method for model.
     */
    public function initialize()
    {
		$this->setSource('Device');

    }

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'ID' => 'ID', 
            'Number' => 'Number', 
            'Type' => 'Type'
        );
    }

}
