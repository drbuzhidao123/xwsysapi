<?php
namespace app\index\controller;

use app\index\controller\Base;
use app\common\lib\IdWork;
use app\common\model\Order as ModelOrder;
use app\common\model\OrderGoods;
use app\common\model\Product;
use app\common\model\Sku;
use app\common\model\SpecsValue;
use think\facade\Cache;

class Order extends Base
{
    public function  add()
    {
        $user_id =  request()->param('user_id');
        $sku_ids =  \trim(request()->param('sku_ids'));
        $total_price =  request()->param('total_price');
        $logistics =  \trim(request()->param('logistics'));
        $address =  \trim(request()->param('address'));
        $work_id = rand(1, 1023);//随机数
        $order_sn = IdWork::getInstance()->setWorkId($work_id)->nextId();//雪花算法生成唯一订单编号
        $order_sn = (string)$order_sn;//把订单号数字转化成字符串，因为数据表里的数据类型是字符串
        $status = 1;
        $created = time();
        $updated = time();
        if(empty($user_id)||empty($total_price)||empty($logistics)||empty($address)||empty($sku_ids)){
            return show(config('status.error'), '参数不完整', null);
        };
        
        $skuObj = new Sku();
        $spcesValueObj = new SpecsValue();
        $proObj = new Product();
        $orderObj = new ModelOrder();
        $orderGoodsObj = new OrderGoods();
        $sku_ids = explode(",", $sku_ids);
        $sku = [];

        //库存是否足够
        foreach($sku_ids as $key=>$vo){
            $sku = $skuObj->getSkuById($vo);   
            $cart_stock = (int)Cache::store('redis')->hget('userCart'.$user_id,$vo);
            if($sku['stock']<$cart_stock){
                return show(config('status.error'), '库存不足', null);
            }         
        }

        $order_data = [
            'user_id' => $user_id,
            'order_id' => $order_sn,
            'status' => $status,
            'total_price' => $total_price,
            'logistics' => $logistics,
            'address' => $address,
            'created' => $created,
            'updated' => $updated,
        ];

        $order_result = $orderObj::create($order_data);

        if(empty($order_result)){
            return show(config('status.error'), '添加订单失败！', null);
        }

        $order_goods_data = [];
        foreach($sku_ids as $key=>$vo){
            $sku = $skuObj::find($vo); 
            $cart_stock = (int)Cache::store('redis')->hget('userCart'.$user_id,$vo);
            $sku_specs = explode(',',$sku['specs_value_id']); 
            $spces_arr = [];
            $specs = [];
            foreach($sku_specs as $spces_key => $spces_val){
                $spces_arr[] = $spcesValueObj->getById($spces_val);
            }
            foreach($spces_arr as $spces_val_key => $specs_value){
                    $specs[] = $specs_value['name'];
            }
            $product = $proObj->getProBySku($vo);
            $order_goods_data[] =  [
                'order_id' => $order_result['order_id'],
                'specs' => \implode(',',$specs),
                'sku_id' => $vo,
                'num' => $cart_stock,
                'price' => $sku['price'],
                'title' =>  $product['title'],
                'image' =>  $product['image'],
            ];
            $stock = $sku['stock'] - $cart_stock;
            $sku->stock = $stock;
            $sku->save();
            $result_cart=Cache::store('redis')->hdel('userCart'.$user_id, $vo);
            if(empty($result_cart)){
                return show(config('status.error'), '购物车删除失败！', $result_cart);
            }
        }

        $res = $orderGoodsObj->saveAll($order_goods_data);

        if(empty($res)){
            return show(config('status.error'), '添加订单失败！', $order_goods_data);
        }
        
        return show(config('status.success'), '添加订单成功！', $order_result['order_id']);
        
    }

    public function getOrderList()
    {
        $user_id = \trim(request()->param('user_id'));
        $pagesize = \trim(request()->param('pagesize'));
        $pagenum = \trim(request()->param('pagenum'));
        $query =   \trim(request()->param('query'));
        $user_id = (Int)$user_id;
        $pagesize = (Int)$pagesize;
        $pagenum = (Int)$pagenum;
        if(empty($pagesize)){
           $pagesize = 1;
        }
        if(empty($pagenum)){
            $pagenum = 6;
         }
        if(empty($user_id)||empty($pagesize)){
            return show(config('status.error'), '没有传用户ID', null);
        }

        $orderObj = new ModelOrder();
        $result = $orderObj->getOrderListByUserId($user_id,$pagenum,$pagesize,$query)->toArray();
        if(empty($result)){
            return show(config('status.error'), '没有订单信息', null);
        }

        return show(config('status.success'), '订单信息获取成功！', $result);

    }

    public function getOrder()
    {
        $user_id =  request()->param('user_id');
        $order_id =  \trim(request()->param('order_id'));
        if(empty($user_id)||empty($order_id)){
            return show(config('status.error'), '没有传用户ID或订单号', null);
        }
        
        $orderObj = new ModelOrder();
        $orderGoodsObj = new OrderGoods();
        $order = $orderObj->getOrder($user_id,$order_id)->toArray();
        $order['created'] = date("Y-m-d H:i:s",$order['created']);
        $orderGoods =  $orderGoodsObj->getOrderGoods($order_id);
        $order['order_goods'] = $orderGoods;
        if(empty($order)){
            return show(config('status.error'), '没有订单信息', null);
        }

        return show(config('status.success'), '订单信息获取成功！', $order);
    }

  
}
