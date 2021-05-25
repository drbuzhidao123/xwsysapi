<?php
namespace app\common\model;

use think\Model;

class AuthGroup extends Model
{
    //获取数据列表
    public function getAuthGroupList($pagenum,$pagesize,$query)
    {
        if(empty($pagenum)&&empty($pagesize)){
             return false;
        }

        if(empty($query)){
        $res = $this->limit(($pagenum-1)*$pagesize,$pagesize)->select();
        }else{
            $where=[
                'title'=>$query
            ];
        $res = $this->where($where)->limit(($pagenum-1)*$pagesize,$pagesize)->select();
        }

        return $res;
       
    }

    public function getAuthGroupTotal($query)
    { 
        if(empty($query)){
            $res = $this->select()->count();
            }else{
                $where=[
                    'title'=>$query
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