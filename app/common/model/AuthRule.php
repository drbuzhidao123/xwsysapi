<?php
namespace app\common\model;

use think\Model;

class AuthRule extends Model
{
    //获取数据列表
    public function getAuthList($pagenum,$pagesize,$query)
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

    public function getAuthTotal($query)
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
    

}