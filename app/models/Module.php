<?php

class Module extends \Phalcon\Mvc\Model
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
    public $Url;

    /**
     *
     * @var string
     */
    public $Icon;

    /**
     *
     * @var integer
     */
    public $ParentID;

    /**
     *
     * @var integer
     */
    public $Sort;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSource('Module');
    }

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'ID' => 'ID', 
            'Name' => 'Name', 
            'Url' => 'Url', 
            'Icon' => 'Icon', 
            'ParentID' => 'ParentID', 
            'Sort' => 'Sort'
        );
    }

}
