<?php
namespace App\HttpModel\User;

use EasySwoole\ORM\AbstractModel;

class AdminModel extends AbstractModel
{
    protected $tableName = 'admin';

    protected $primaryKey = 'id';

    #自动更新时间戳
    protected $autoTimeStamp = true;
    protected $createTime = 'create_at';
    protected $updateTime = 'update_at';

    public function _list(int $page = 1, string $userName = null, string $realName = null, int $pageSize = 10): array
    {
        $where = [];
        if (!empty($userName)) {
            $where['username'] = ['%' . $userName . '%', 'like'];
        }
        if (!empty($realName)) {
            $where['real_name'] = ['%' . $realName . '%', 'like'];
        }
        $list  = $this->limit($pageSize * ($page - 1), $pageSize)->order($this->primaryKey, 'DESC')->withTotalCount()->all($where);
        $total = $this->lastQueryResult()->getTotalCount();
        return ['total' => $total, 'list' => $list, 'page' => $page, 'pageSize' => $pageSize];
    }

    /*
     * 登录成功后请返回更新后的bean
     */
    public function login():?AdminModel
    {
        $info = $this->get(['username' => $this->username, 'password' => $this->password, 'real_name' => $this->real_name]);
        return $info;
    }

    /*
     * 以username进行查询
     */
    public function accountExist($field = '*'):?AdminModel
    {
        $info = $this->field($field)->get(['username' => $this->username]);
        return $info;
    }

    public function getOneBySession($field = '*'):?AdminModel
    {
        $info = $this->field($field)->get(['session' => $this->session]);
        return $info;
    }

    public function logout()
    {
        return $this->update(['session' => '']);
    }
}
