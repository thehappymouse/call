<?php

use Phalcon\Mvc\Model;

class Menu extends Model
{
    /**
     * @param null $parameters
     * @return Arrears
     */
    public static function findFirst($parameters=null){
        return parent::findFirst($parameters);
    }

    /**
     *
     * @var integer
     */
    public $ID;

    /**
     *
     * @var integer
     */
    public $LineNumber;

    /**
     *
     * @var string
     */
    public $Name;

    /**
     *
     * @var integer
     */
    public $Type;

    /**
     *
     * @var string
     */
    public $TypeName;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSource('Menu');
    }

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'ID' => 'ID', 
            'Name' => 'Name', 
            'Type' => 'Type', 
            'LineNumber' => 'LineNumber',
            'TypeName' => 'TypeName'
        );
    }
}