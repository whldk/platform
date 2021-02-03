<?php namespace App\HttpModel\User;

use EasySwoole\ORM\AbstractModel;

class SchoolModel extends AbstractModel
{
    protected $tableName = 'school';
    protected $primaryKey = 'id';

    #自动更新时间戳
    protected $autoTimeStamp = true;
    protected $createTime = 'create_at';
    protected $updateTime = 'update_at';

    //利用cast定义可以实现：在取出时自动转换为数组、在存储时自动转换为json字符
    protected $casts = [
        'id' => 'int',
        'name' => 'string',
        'alias' => 'string',
        'logo' => 'string',
        'login_num' => 'string',
        'create_at' => 'int',
        'update_at' => 'int',
        'status' => 'int'
    ];

    public function list(string $name = null, string $status = null, int $page = 1, int $pageSize = 10):array
    {
        $where = [];
        if (!empty($name)) {
            $where['name'] = ['%' . $name . '%', 'like'];
        }
        if (!empty($status)) {
            $where['status'] = $status;
        }
        $list  = $this->limit($pageSize * ($page - 1), $pageSize)->order($this->primaryKey, 'DESC')->withTotalCount()->all($where);
        $total = $this->lastQueryResult()->getTotalCount();
        return ['total' => $total, 'list' => $list, 'page' => $page , 'pagesize' => $pageSize, 'total_page' => ceil($total / $pageSize)];
    }

    public function getSchoolAlias():?SchoolModel
    {
        $info = $this->get(['alias' => $this->alias]);
        return $info;
    }
}