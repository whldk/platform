<?php
namespace App\HttpModel\User;

use EasySwoole\ORM\AbstractModel;

class UserModel extends AbstractModel
{
    protected $tableName = 'user';

    #自动更新时间戳
    protected $autoTimeStamp = true;
    protected $createTime = 'create_at';
    protected $updateTime = 'update_at';

    //利用cast定义可以实现：在取出时自动转换为数组、在存储时自动转换为json字符
    protected $casts = [
        'id' => 'int',
        'username' => 'string',
        'password' => 'string',
        'real_name' => 'string',
        'create_at' => 'timestamp',
        'update_at' => 'timestamp'
    ];

}