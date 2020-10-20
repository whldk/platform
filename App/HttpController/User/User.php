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

    }

    public function create()
    {
        $valid = UserRules::check($this->params);
        if ($valid->isFail()) {
            var_dump($valid->getErrors());
            var_dump($valid->firstError());
        }
        // 验证成功 ...
        $safeData = $valid->getSafeData(); // 验证通过的安全数据
        // $postData = $v->all(); // 原始数据
        var_dump($safeData);
    }

    public function update()
    {

    }

    public function delete()
    {

    }

    public function view()
    {
        $valid = Validation::make($this->params, ['id', 'required', 'msg' => ['id' => '用户ID必填']]);
        if ($valid->isFail()) {
            return $this->writeJson(Status::CODE_BAD_REQUEST,$valid->getErrors());
        }
        $res = UserModel::create()->get(['id', $this->params['id']]);
        return $this->writeJson(Status::CODE_OK, $res);
    }
}