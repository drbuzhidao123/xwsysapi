<?php
namespace app\common\model;

use think\Model;

class OrderGoods extends Model
{  
    protected $type = [
        'price'    =>  'float',
    ];

    public function getOrderGoods($order_id)
    {
        $where = [
            'order_id' =>$order_id,
        ];
        $result = $this->where($where)->select()->toArray();    
        return $result;
    }


}