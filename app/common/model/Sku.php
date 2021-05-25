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


  
    

}