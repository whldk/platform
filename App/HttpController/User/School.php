<?php namespace App\HttpController\User;

use App\HttpController\BaseController;
use App\HttpModel\User\SchoolModel;
use EasySwoole\Validate\Validate;
use Swoole\Http\Status;

class School extends BaseController
{
    public $access = [
        '*' => ['admin'],
        'category' => ['*']
    ];

    public function onRequest(?string $action): ?bool
    {
        $ret =  parent::onRequest($action);
        if($ret === false){
            return false;
        }
        $v = $this->validateRule($action);
        if($v){
            $ret = $this->validate($v);
            if($ret == false){
                $this->writeJson(Status::BAD_REQUEST,null,"{$v->getError()->getFieldAlias()}{$v->getError()->getErrorRuleMsg()}");
                return false;
            }
        }
        return true;
    }

    public function validateRule(?string $action):? Validate
    {
        $v = new Validate();
        switch ($action) {
            case 'create' :
                $v->addColumn('name','学校名称')->required('不能为空');
                $v->addColumn('alias', '学校简称')->required('不能为空');
                break;
            case 'update':
            case 'view':
            case 'delete':
                 $v->addColumn('id','学校id')->required('不能为空');
                break;
        }
        return $v;
    }

    public function category()
    {

    }

    public function list()
    {
        $page = $this->params['page'] ?? 1;
        $pageSize = $this->params['pagesize'] ?? 10;
        $name = $this->params['name'] ?: null;
        $model = new SchoolModel();
        $data = $model->list($name, $page, $pageSize);
        $this->writeJson(Status::OK, $data, 'success');
    }

    public function create()
    {
        $model = new SchoolModel();
        $model->name = $this->params['name'];
        $model->alias = $this->params['alias'];
        $model->logo = $this->params['logo'];
        $model->login_num = $this->params['login_num'] ?? 100;
        $model->status = $this->params['status'] ?? 0;
        if ($user = $model->create()) {
            $lastResult = $model->lastQueryResult();
            $this->writeJson(Status::OK, ['id' => $lastResult->getLastInsertId()], 'success');
        } else {
            $lastResult = $model->lastQueryResult();
            $this->writeJson(Status::BAD_REQUEST, '', $lastResult->getLastError());
        }
    }

    public function view()
    {
        $res = SchoolModel::create()->get($this->params['id']);
        $this->writeJson(Status::OK, $res, 'success');
    }

    public function update()
    {
        $school = SchoolModel::create()->get($this->params['id']);
        //获取后指定字段赋值
        $school->status = $this->params['status'] ?? $school['0']['status'];
        $school->name = $this->params['name'] ?? $school['0']['name'];
        $school->alias = $this->params['alias'] ?? $school['0']['alias'];
        $school->logo = $this->params['logo'] ?? $school['0']['logo'];
        $school->login_num = $this->params['login_num'] ?? $school['0']['login_num'];
        $school->update();
        $lastResult = $school->lastQueryResult();
        $this->writeJson(Status::OK, ['id' => $lastResult->getLastInsertId()], 'success');
    }

    public function delete()
    {
        $school = SchoolModel::create()->get($this->params['id']);
        $school->destroy();
        $lastResult = $school->lastQueryResult();
        $this->writeJson(Status::OK, '', $lastResult);
    }

}