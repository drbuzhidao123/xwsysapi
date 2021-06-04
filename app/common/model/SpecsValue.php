<?php
namespace app\common\model;
use think\facade\Db;
use think\Model;

class SpecsValue extends Model
{
    //获取数据列表
    public function getList($pagenum,$pagesize,$query,$specs_id)
    {
        if(empty($pagenum)&&empty($pagesize)){
             return false;
        }

        if(empty($query)){
            $where=[
                'specs_id'=>$specs_id
            ];
        }else{
            $where=[
                'specs_id'=>$specs_id,
                'name'=>$query
            ];
        }
        $res = $this->where($where)->limit(($pagenum-1)*$pagesize,$pagesize)->select();

        return $res;  
    }

    public function getSpecs($data)
    {
        /*$res = Db::table('specs_value')->alias('sv')
        ->LeftJoin('specs s','sv.specs_id = s.id')
        ->distinct(true)
        ->field('sv.id,sv.name,s.name as sname,sv.specs_id')
        ->select();*/
        $res=[];
        foreach($data as $key=>$vo){
            $res[$key]['sname']=$vo['name'];
            $res[$key]['svalue']= $this->where('specs_id',$vo['id'])->field('id,name')->select();
            /*foreach($res[$key]['svalue'] as $svkey=>$svvo){
                   $svvo['sname'] = $res[$key]['sname'];
            }*/
        };
       
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

    public function getNormalInIds($ids)
    {
        $result=$this->whereIn('id',$ids)->select();
        return $result;
    }

    public function getById($id)
    {
        if(empty($id)){
            return false;
        }

        $res = $this->where('id',$id)->find()->toArray(); 
        return $res;
    }

  
    

}