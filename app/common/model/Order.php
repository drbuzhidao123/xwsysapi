<?php

namespace app\common\model;

use think\Model;

class Order extends Model
{
    protected $type = [
        'total_price'    =>  'float',
    ];

    public function getOrder($user_id, $order_id)
    {

        /*$result = Db::table('order')
            ->alias('o')
            ->join(['order_goods' => 'g'], 'o.order_id=g.order_id')
            ->where('o.user_id', $user_id)
            ->where('o.order_id', $order_id)
            ->select()
            ->toArray();*/
        $where = [
            'user_id' => $user_id,
            'order_id' =>$order_id,
        ];
        $result = $this->where($where)->find();    

        return $result;
    }

    public function getOrderListByUserId($user_id,$pagenum,$pagesize,$query)
    {
        $where = [
            'user_id' => $user_id,
        ];
        if(!empty($query)){
            $result = $this->where($where)->where('order_id', 'like', '%'.$query.'%')->limit(($pagenum - 1) * $pagesize, $pagesize)->select();  
        }else{
            $result = $this->where($where)->limit(($pagenum - 1) * $pagesize, $pagesize)->select();    
        }

        return $result;
    }

    public function updateOnPayClose($order_id,$pay_type)
    {
        if(empty($order_id)){
            return false;
        }

        $order = $this->where('order_id',$order_id)->find();
        $order->status = 2;
        $order->pay_type = $pay_type;
        $order->updated = time();
        $order->paytime = time();
        $order->end_time = time();
        $order->close_time = time();
        $res = $order->save();
        return $res;

    }


}
