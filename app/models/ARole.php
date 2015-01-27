<?php


class ARole extends \Phalcon\Mvc\Model
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
    public $Name;

    /**
     *
     * @var string
     */
    public $Modules;

    /**
     *
     * @var string
     */
    public $IndexPage;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSource('Role');
    }

    /**
     * @param null $parameters
     * @return ARole
     */
    public static function findFirst($parameters=null){
        return parent::findFirst($parameters);
    }

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'ID' => 'ID', 
            'Name' => 'Name', 
            'IndexPage' => 'IndexPage',
            'Modules' => 'Modules'
        );
    }

}
