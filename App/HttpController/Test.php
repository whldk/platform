<?php namespace App\HttpController;

use App\Model\Admin\BannerModel;
use EasySwoole\Http\AbstractInterface\Controller;
use EasySwoole\Http\Message\Status;

class Test extends Controller
{
    public function index()
    {
        $this->response()->write('hello world test');
    }

    public function getAll()
    {
        $param = $this->request()->getRequestParam();
        $page = $param['page']??1;
        $limit = $param['limit']??20;
        var_dump($param);
        $model = new BannerModel();
        $data = $model->getAll($page, 1,$param['keyword']??null, $limit);
        $this->writeJson(Status::CODE_OK, $data, 'success');
    }
}