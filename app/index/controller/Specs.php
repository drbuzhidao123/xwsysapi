<?php
namespace app\index\controller;

use app\BaseController;
use app\common\model\Sku as ModelSku;
use app\common\model\Specs as ModelSpecs;
use app\common\model\SpecsValue;

class Specs extends BaseController
{
    public function getSpecs()
    {
        $id =  \trim(request()->param('id'));
        $sku = new ModelSku();
        $gids = $sku->getgids($id);
        $specsValueKeys=\array_keys($gids);
        foreach($specsValueKeys as $specsValueKey){
               $specsValueKey = explode(',',$specsValueKey);
               foreach($specsValueKey as $k=>$v){
                     $new[$k][] = $v;
                     $specsValueIds[] = $v;
               }
        }
        $specsValueIds = \array_unique($specsValueIds);
        $specsValues = $this->getNormalInIds($specsValueIds);//处理数据
        $result = [];
        foreach($new as $key=>$newValue){
            $newValue = \array_unique($newValue);
            $list = [];
            foreach($newValue as $vv){
                  $list[] = [
                      'id'=>$vv,
                      'name'=>$specsValues[$vv]['name'],
                  ];
            }
            
            $result[$key] = [
                'name' => $specsValues[$newValue[0]]['specs_name'],
                'list' => $list,
            ];
        }
        if (empty($result)) {
            return show(config('status.error'), '没有数据', $result);
        }
        return show(config('status.success'), '查询数据成功', $result);
    }

    public function getNormalInIds($ids)
    {
        if(!$ids){
             return [];
        }
        try{
             $SpecsValueObj = new SpecsValue();
             $result = $SpecsValueObj->getNormalInIds($ids);
        }catch (\Exception $e){
            return [];
        }
        $result= $result->toArray();
        if(!$result){
            return [];
        }

        $spcesObj = new ModelSpecs();
        $specsNames = $spcesObj->select()->toArray();
        $specsNames = \array_column($specsNames,'name','id');
        $res=[];
        foreach($result as $resultValue){
             $res[$resultValue['id']]=[
                 'name'=>$resultValue['name'],
                 'specs_name' => $specsNames[$resultValue['specs_id']] ?? "",
             ];
        }

        return $res;

    }

  
}
