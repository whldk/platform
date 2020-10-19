<?php namespace App\HttpController;

use App\Model\Admin\BannerModel;
use EasySwoole\Http\AbstractInterface\Controller;
use EasySwoole\Http\Message\Status;

class Banner extends Controller
{
    public function getOne()
    {
        $param = $this->request()->getRequestParam();
        $model = new BannerModel();
        $id = isset($param['id']) ? $param['id'] : null;
        $bean = $model->get($id);
        if ($bean) {
            $this->writeJson(Status::CODE_OK, $bean, "success");
        } else {
            $this->writeJson(Status::CODE_BAD_REQUEST, [], 'fail');
        }
    }

    public function getAll()
    {
        $param = $this->request()->getRequestParam();
        $page = $param['page']??1;
        $limit = $param['limit']??20;
        $model = new BannerModel();
        $data = $model->getAll($page, 1,$param['keyword']??null, $limit);
        $this->writeJson(Status::CODE_OK, $data, 'success');
    }

    public function test()
    {
        $this->response()->write('hello world test');
    }
}