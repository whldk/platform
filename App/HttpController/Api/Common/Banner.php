<?php namespace App\HttpController\Api\Common;

use App\Model\Admin\BannerModel;
use EasySwoole\Http\Message\Status;

class Banner extends CommonBase
{
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

}