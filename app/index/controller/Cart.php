<?php
namespace app\index\controller;

use app\common\model\Product;
use app\common\model\Sku;
use app\common\model\SpecsValue;
use app\index\controller\Base;
use think\facade\Cache;

class Cart extends Base
{
    public function add()
    {
        $userId =  request()->param('userId');
        $skuId =  request()->param('skuId');;
        $shopNum = request()->param('shopNum');;
        if(!$userId||!$shopNum||!$skuId){
            return show(config('status.error'), '参数不合法', null);
        }
        $cacheValue=Cache::store('redis')->hget('userCart'.$userId,$skuId);
        if(empty($cacheValue)){
            Cache::store('redis')->hsetnx('userCart'.$userId,$skuId,$shopNum);
            Cache::store('redis')->expire('userCart'.$userId,86400);   
            return show(config('status.success'), '成功添加到购物车！', null);
        }else{
            $shopNum = $shopNum + $cacheValue;
            $cacheValue[$skuId] = Cache::store('redis')->hset('userCart'.$userId,$skuId,$shopNum);
            Cache::store('redis')->expire('userCart'.$userId,86400);  
            return show(config('status.success'), '成功添加到购物车！', null);
        } 
    }

    public function getCartList()
    {
        $userId =  request()->param('userId');
        $cacheValue=Cache::store('redis')->hgetall('userCart'.$userId);
        $ProductObj = new Product();
        $SkuObj = new Sku();
        $SpecsValueObj = new SpecsValue();
        $cart=[];
        foreach($cacheValue as $skuId=>$num){
            $cart[$skuId] = $ProductObj->getProBySku($skuId);
            $cart[$skuId]['shopNum'] =(int)$num;
            $sku = $SkuObj->getSkuById($skuId);
            $cart[$skuId]['skuId'] = $skuId;
            $cart[$skuId]['price'] = (double)$sku['price'];
            $cart[$skuId]['acount'] = $cart[$skuId]['price']*$cart[$skuId]['shopNum'];
            $specsVidsArr = explode(',',$sku['specs_value_id']);
            foreach($specsVidsArr as $key=>$vo){
                $cart[$skuId]['specsValue'][$key]=$SpecsValueObj->getById($vo);
            }
        }
        
        if(!empty($cart)){
            return show(config('status.success'), '获取购物车列表成功', $cart);
        }
    }


    public function delete()
    {
        $userId =  request()->param('userId');
        $skuId = request()->param('skuId');
        if(!$userId||!$skuId){
            return show(config('status.error'), '没有商品id或用户id', null);
        }
        $result=Cache::store('redis')->hdel('userCart'.$userId,$skuId);
        if(!empty($result)&&$result>0){
            return show(config('status.success'), '删除'.$skuId.'成功！', $result);
        }else{
            return show(config('status.success'), '删除'.$skuId.'失败！购物车里已经没有此商品！', $result);
        }
    }

  
}
