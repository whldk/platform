<?php
namespace App\HttpModel\User;

use EasySwoole\ORM\AbstractModel;
use Swoole\Table;

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

    public function schemaInfo(bool $isCache = true): Table
    {
        $table = new Table($this->tableName);
        $table->colInt('id')->setIsPrimaryKey(true);
        $table->colChar('username', 255);
        $table->colChar('password', 255);
        $table->colChar('real_name', 255);
        $table->colInt('create_at');
        $table->colInt('update_at');
        return $table;
    }
}