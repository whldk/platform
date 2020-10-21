<?php namespace App\HttpController\User;

use App\HttpController\Common\BaseController;

use App\Model\User\UserModel;
use App\Rules\UserRules;
use EasySwoole\Http\Message\Status;
use Inhere\Validate\Validation;

class User extends BaseController
{

    protected $access = [
        ''
    ];

    public function category()
    {
        return $this->writeJson(200,'category', 'success');
    }

    public function list()
    {
        $page = 1;          // 当前页码
        $limit = 10;        // 每页多少条数据
        $model = UserModel::create()->limit($limit * ($page - 1), $limit)->withTotalCount();
        $where = null;
        $list = $model->all($where);
        $result = $model->lastQueryResult();
        $total = $result->getTotalCount();
        $res = [$list, $result, $total];
        return $this->writeJson(Status::CODE_OK, $res, '获取用户列表');
    }

    public function create()
    {
        $valid = UserRules::make($this->params)->validate(['create'],false);
        if ($valid->isFail()) {
            return $this->writeJson(Status::CODE_BAD_REQUEST,$valid->getErrors(), '创建用户');
        }
        //验证成功 ...
        //$postData = $v->all(); // 原始数据
        $safeData = $valid->getSafeData(); // 验证通过的安全数据
        $model = UserModel::create($safeData);
        $res = $model->save();
        return $this->writeJson(Status::CODE_OK, $res, '创建用户成功');
    }

    public function update()
    {
        $valid = UserRules::make($this->params)->validate(['update'],false);
        if ($valid->isFail()) {
            return $this->writeJson(Status::CODE_BAD_REQUEST,$valid->getErrors(), '更新用户');
        }
        //验证成功 ...
        //$postData = $v->all(); // 原始数据
        $safeData = $valid->getSafeData(); // 验证通过的安全数据
        $model = UserModel::create($safeData);
        $res = $model->save();
        return $this->writeJson(Status::CODE_OK, $res, '更新用户成功');
    }

    public function delete()
    {
        $valid = UserRules::make($this->params)->validate(['delete'],false);
        if ($valid->isFail()) {
            return $this->writeJson(Status::CODE_BAD_REQUEST,$valid->getErrors(), '删除用户');
        }
        //验证成功 ...
        $safeData = $valid->getSafeData();
        $model = UserModel::create()->get($safeData['id']);
        $res = $model->destroy();
        return $this->writeJson(Status::CODE_OK, $res, '删除用户成功');
    }

    public function view()
    {
        $valid = UserRules::make($this->params)->validate(['view'], false);
        if ($valid->isFail()) {
            return $this->writeJson(Status::CODE_BAD_REQUEST,$valid->getErrors());
        }
        $res = UserModel::create()->get(['id', $this->params['id']]) ?: [];
        return $this->writeJson(Status::CODE_OK, $res, '查看用户');
    }
}