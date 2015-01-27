<?php

class User extends \Phalcon\Mvc\Model
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
    public $Pass;

    /**
     *
     * @var integer
     */
    public $Role;

    /**
     *
     * @var string
     */
    public $CreateTime;

    /**
     * @var string
     */
    public $RoleName;

    /**
     *
     * @var integer
     */
    public $CreateUser;

    /**
     *
     * @var integer
     */
    public $TeamID;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSource('User');
        $this->belongsTo('TeamID', 'Team', 'ID');
    }

    /**
     * @param null $parameters
     * @return Arrears
     */
    public static function findFirst($parameters=null){
        return parent::findFirst($parameters);
    }

    /**
     * 查询uid是否表示全部。  int表示否；tid_1 表示全部，1表示组的ID号
     * @param $uid
     * @return bool|int
     */
    public static function IsAllUsers($uid){
        if(preg_match('/^tid_(-{0,1}\d{1,})/', $uid, $matches) || preg_match('/^t_(-{0,1}\d{1,})/', $uid, $matches)){
            return $matches[1];
        }
        else {
            return false;
        }
    }

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'ID' => 'ID', 
            'Name' => 'Name', 
            'Pass' => 'Pass', 
            'Role' => 'Role', 
            'RoleName' => 'RoleName',
            'CreateTime' => 'CreateTime',
            'CreateUser' => 'CreateUser', 
            'TeamID' => 'TeamID'
        );
    }

}
