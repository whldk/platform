<?php namespace App\User;

use EasySwoole\ORM\AbstractModel;
use EasySwoole\ORM\Utility\Schema\Table;

class UserModel extends AbstractModel
{
    protected $tableName = 'user';

    public function schemaInfo(bool $isCache = true): Table
    {
        $table = new Table($this->tableName);
        $table->colInt('_id')->setIsNotNull()->setIsAutoIncrement()->setIsPrimaryKey(true);
        $table->colVarChar('id')->setColumnCharset('utf8 utf8_bin')->setColumnLimit(255);
        $table->colVarChar('username')->setColumnLimit(255);
        $table->colVarChar('password',255);
        return $table;
    }
}