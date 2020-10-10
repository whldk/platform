<?php namespace App\HttpController;

use App\Model\Admin\BannerModel;
use EasySwoole\Http\AbstractInterface\Controller;
use EasySwoole\Http\Message\Status;

class Ces extends Controller
{
    public function index()
    {
        $this->response()->write('hello world test');
    }

    public function getOne()
    {
        $param = $this->request()->getRequestParam();
        $model = new BannerModel();
        $bean = $model->get($param['bannerId']);
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

    public function getData()
    {
        $config = new \EasySwoole\Mysqli\Config([
            'host'          => '47.102.96.5',
            'port'          => 3300,
            'user'          => 'root',
            'password'      => 'Mengoo2020!',
            'database'      => 'plathform',
            'timeout'       => 5,
            'charset'       => 'utf8mb4',
        ]);

        $client = new \EasySwoole\Mysqli\Client($config);
        go(function ()use($client){
            //构建sql
            $client->queryBuilder()->get('user_list');
            //执行sql
            $client->execBuilder();
        });
    }
}