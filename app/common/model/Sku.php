<?php
namespace app\common\model;

use think\Model;

class Sku extends Model
{
    public function getList()
    {
        $res = $this->select();   
        return $res;
    }

    public function getgids($proId)
    {
        if(empty($proId)){
            return false;
        }

        $sku = $this->where('product_id',$proId)->select()->toArray();
        $gids=\array_column($sku,'id','specs_value_id');
        return $gids;
    }

    public function getSkuBySpecsValueId($specsValueId,$proId)
    {
        if(empty($specsValueId)||empty($proId)){
            return false;
        }

        $where=[
            'specs_value_id' => $specsValueId,
            'product_id' => $proId,
        ];

        $res=$this->where($where)->find(); 
        return $res;
    }


    public function getSkuById($id)
    {
        if(empty($id)){
            return false;
        }

        $res = $this->where('id',$id)->find();   
        return $res;
    }

  
    

}