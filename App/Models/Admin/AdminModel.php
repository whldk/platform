<?php namespace App\Models\Admin;

use EasySwoole\ORM\AbstractModel;

/**
 * Class AdminModel
 * @property $_id
 * @property $nickname
 * @property $username
 * @property $password
 * @property $session
 * @property $last_login_time
 * @property $last_login_ip
 */
class AdminModel extends AbstractModel
{
    protected  $tableName = 'admin';

    protected $primaryKey = '_id';

    public function getAll(int $page = 1, string $keyword = null, int $pageSize = 10): array
    {
        $where = [];
        if (!empty($keyword)) {
            $where['username'] = ['%' . $keyword . '%', 'like'];
        }
        $list  = $this->limit($pageSize * ($page - 1), $pageSize)->order($this->primaryKey, 'DESC')->withTotalCount()->all($where);
        $total = $this->lastQueryResult()->getTotalCount();
        return ['total' => $total, 'list' => $list];
    }

    /*
     * 登录成功后请返回更新后的bean
     */
    public function login():?AdminModel
    {
        return $this->get(['username' => $this->username, 'password' => $this->password]);
        return $info;
    }

    /*
     * 以account进行查询
     */
    public function usernameExist($field = '*'):?AdminModel
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