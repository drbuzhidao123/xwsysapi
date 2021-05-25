<?php

namespace app\admin\controller;
use app\admin\controller\Base;
use app\common\model\Sku as ModelSku;
use think\facade\Request;

class Sku extends Base
{
    public function getSkuByProId()
    {
        $param = Request::param();
        if($param){
            $skuObj = new ModelSku();
            $res = $skuObj->where('product_id',$param['id'])->select()->toArray();
        }
        if (empty($res)) {
            return show(config('status.error'), '没有sku数据', $res);
        }
        return show(config('status.success'), '查询sku成功', $res);
    }

}
