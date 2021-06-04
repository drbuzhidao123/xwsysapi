<?php
namespace app\index\controller;

use app\BaseController;
use app\common\model\Sku as ModelSku;

class Sku extends BaseController
{
    public function getSkuData()
    {
        $selectParam =  request()->param('selectParam');
        $proId =  request()->param('proId');
        if(!$selectParam||!$proId){
            return show(config('status.error'), '参数为空', null);
        }
        $skuObj = new ModelSku();
        $res = $skuObj->getSkuBySpecsValueId($selectParam,$proId);
        if($res){
            return show(config('status.success'), '查询数据成功', $res);
        }else{
            return show(config('status.success'), '查询数据失败', null);
        }
    }

  
}
