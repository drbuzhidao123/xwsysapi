<?php
namespace app\common\model;

use think\Model;

class Specs extends Model
{
    public function getList()
    {
        $res = $this->select();   
        return $res;
    }

    public function getTotal($query)
    { 
        if(empty($query)){
            $res = $this->select()->count();
            }else{
                $where=[
                    'name'=>$query
                ];
            $res = $this->where($where)->select()->count();
            }
        return $res;
    }

    public function updateById($id,$authGroup)
    {
        if(empty($id)){
         return false;
        }

        $where = [
            'id' => $id
        ];

        $res = $this->where($where)->save($authGroup);
        return $res;
        
    }

  
    

}