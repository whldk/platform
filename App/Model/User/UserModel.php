<?php namespace App\Model\User;

use EasySwoole\ORM\AbstractModel;

class UserModel extends AbstractModel
{
    protected $tableName = 'user';
    protected $autoTimeStamp = true;
    protected $createTime = 'created_at';
    protected $updateTime = 'updated_at';
    public $casts = [
        'id' => 'string',
        'school_id' => 'string',
        'username' => 'string',
        'password' => 'string',
        'real_name' => 'string',
        'group' => 'int',
        'avatar' => 'string',
        'status' => 'boolean',
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
        'session'   => 'string',
        'login_ip'  => 'string',
        'login_time' => 'timestamp'
    ];
    

}