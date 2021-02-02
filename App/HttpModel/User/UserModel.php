<?php
namespace App\HttpModel\User;

use EasySwoole\ORM\AbstractModel;

class UserModel extends AbstractModel
{
    protected $tableName = 'user';
    protected $primaryKey = 'id';

    #自动更新时间戳
    protected $autoTimeStamp = true;
    protected $createTime = 'create_at';
    protected $updateTime = 'update_at';

    //利用cast定义可以实现：在取出时自动转换为数组、在存储时自动转换为json字符
    protected $casts = [
        'id' => 'int',
        'username' => 'string',
        'password' => 'string',
        'realName' => 'string',
        'session' => 'string',
        'group' => 'int',
        'create_at' => 'timestamp',
        'update_at' => 'timestamp'
    ];

    public function _list(string $userName = null, string $realName = null,  string $group = null, int $page = 1, int $pageSize = 10): array
    {
        $where = [];
        if (!empty($userName)) {
            $where['username'] = ['%' . $userName . '%', 'like'];
        }
        if (!empty($realName)) {
            $where['realName'] = ['%' . $realName . '%', 'like'];
        }
        if (!empty($group)) {
            $where['group'] = $group;
        }
        $list  = $this->limit($pageSize * ($page - 1), $pageSize)->order($this->primaryKey, 'DESC')->withTotalCount()->all($where);
        $total = $this->lastQueryResult()->getTotalCount();
        return ['total' => $total, 'list' => $list, 'page' => $page, 'pageSize' => $pageSize];
    }

    /*
    * 登录成功后请返回更新后的bean
    */
    public function login():?UserModel
    {
        $info = $this->get(['username' => $this->username, 'password' => $this->password]);
        return $info;
    }

    public function getOneBySession($field = '*'):?UserModel
    {
        $info = $this->field($field)->get(['session' => $this->session]);
        return $info;
    }

    public function logout()
    {
        return $this->update(['session' => '']);
    }

}