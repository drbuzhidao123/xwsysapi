<?php
namespace app\common\controller;

class Tool 
{
    public function tree($arr,$pid)
    {
        $tree = [];
        foreach($arr as $key=>$val) {
            if($val['pid'] == $pid) {
                if(!empty($this->tree($arr,$val['id']))){
                    $val['children'] = $this->tree($arr,$val['id']);   
                }
                $tree[] = $val;
                //$tree = \array_merge($tree,$this->tree($arr, $val['id']));
            }
        }
        return $tree;
    }

    public function editTree($arr,$pid,$id)
    {
        $tree = [];
        foreach($arr as $key=>$val) {
            if($val['pid'] == $pid) {
                if($val['id']==$id){
                }else{
                    if(!empty($this->editTree($arr,$val['id'],$id))){
                        $val['children'] = $this->tree($arr,$val['id']);   
                    }
                    $tree[] = $val;
                }
                //$tree = \array_merge($tree,$this->tree($arr, $val['id']));
            }
        }
        return $tree;
    }
  
}
