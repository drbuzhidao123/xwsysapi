<?php
namespace app\common\model;

use think\Model;

class ProductCate extends Model
{
    //获取数据列表
    public function getProCateList($query)
    {
       

        if(empty($query)){
        $res = $this->select();
        }else{
            $where=[
                'title'=>$query
            ];
        $res = $this->where($where)->select();
        }

        return $res;
       
    }

    public function getProCateTotal($query)
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


    public function updateById($id,$procate)
    {
        if(empty($id)){
         return false;
        }

        $where = [
            'id' => $id
        ];

        $res = $this->where($where)->save($procate);
        return $res;
        
    }

    public function remove($id)
    {
        if(empty($id)){
            return false;
           }
        $strid = \strval($id).',';
        $res=$this->where('family','like','%'.$strid.'%')->delete();
        return $res;
    }



}