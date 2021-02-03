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
                $v->addColumn('alias', '学校简称')->func(function($data, $name){
                    $res = SchoolModel::create()->get(['alias' => $data[$name]]);
                    return $res ? false : true;
                }, '不能重复');
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
        $res = SchoolModel::create()->all(['status' => 1])->visible(['id','name', 'alias'])->toArray();;
        $this->writeJson(Status::OK, $res ?: [], 'success');
    }

    public function list()
    {
        $page = $this->params['page'] ?? 1;
        $pageSize = $this->params['pagesize'] ?? 10;
        $name = $this->params['name'] ?? null;
        $status = $this->params['status'] ?? null;
        $model = new SchoolModel();
        $data = $model->list($name, $status, $page, $pageSize);
        $this->writeJson(Status::OK, $data, 'success');
    }

    public function create()
    {
        $model = new SchoolModel();
        $model->name = $this->params['name'];
        $model->alias = $this->params['alias'];
        $file = $this->params['logo'] ?? null;
        if ($file) {
            $md5file = md5_file($file->getTempName());
            $ext = pathinfo($file->getClientFilename(), PATHINFO_EXTENSION);
            $url = UPLOAD_DIR . $md5file. '.' .$ext;
            $file->moveTo($url);
            $model->logo = '/upload/'. $md5file. '.' .$ext;
        }
        $model->login_num = $this->params['login_num'] ?? 100;
        $model->status = $this->params['status'] ?? 1;

        if ($res = $model->save()) {
            if ($res) {
                $this->writeJson(Status::OK,  $res, 'success');
            } else {
                $this->writeJson(Status::BAD_REQUEST, $res,  'fail');
            }
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
        if ($school !== null) {
            $school->status = $this->params['status'] ?? $school->status;
            $school->name = $this->params['name'] ?? $school->name;
            $school->alias = $this->params['alias'] ?? $school->alias;
            $school->update_at = time();
            $file =  $this->params['logo'] ?? $school->logo;
            if ($file) {
                $md5file = md5_file($file->getTempName());
                $ext = pathinfo($file->getClientFilename(), PATHINFO_EXTENSION);
                $url = UPLOAD_DIR . $md5file. '.' .$ext;
                $file->moveTo($url);
                $school->logo = '/upload/'. $md5file. '.' .$ext;
            }
            $school->login_num = $this->params['login_num'] ?? $school->login_num;
            $school->update();
            $this->writeJson(Status::OK, 1, 'success');
        } else {
            $this->writeJson(Status::BAD_REQUEST, 0, 'fail');
        }

    }

    public function delete()
    {
        $school = SchoolModel::create()->get($this->params['id']);
       if (!$school) {
           $this->writeJson(Status::BAD_REQUEST, 0, 'fail');
       } else {
           $school->destroy();
           $this->writeJson(Status::OK, 1, 'success');
       }
    }

}