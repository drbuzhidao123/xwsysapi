<?php

namespace app\common\model;

use think\Model;

class Product extends Model
{
    public function productCate()
    {
        return $this->belongsTo(ProductCate::class, 'cate_id');
    }

    public function Sku()
    {
        return $this->hasMany(Sku::class, 'product_id');
    }

    public function getList($cate_id, $pagenum, $pagesize, $query = null)
    {
        if (empty($pagenum) && empty($pagesize)) {
            return false;
        }
        if (empty($query)) {
            $where = [
                'cate_id' => $cate_id
            ];
            $res = $this->where($where)->limit(($pagenum - 1) * $pagesize, $pagesize)->select();
        } else {
            $where = [
                'title' => $query,
                'cate_id' => $cate_id
            ];
            $res = $this->where($where)->limit(($pagenum - 1) * $pagesize, $pagesize)->select();
        }
        return $res;
    }

    public function getList_home($cate_id, $pagenum, $pagesize, $query = null)
    {
        if (empty($pagenum) && empty($pagesize)) {
            return false;
        }

        if ($cate_id == 'all') {
            if (empty($query)) {
                $res = $this->limit(($pagenum - 1) * $pagesize, $pagesize)->select()->toArray();
            } else {
                $where = [
                    'title' => $query
                ];
                $res = $this->limit(($pagenum - 1) * $pagesize, $pagesize)->select()->toArray();
            }
        } else {
            if (empty($query)) {
                $res = $this->hasWhere('productCate', function ($query) use ($cate_id) {
                    $query->where('family', 'like', '%' . $cate_id . ',%');
                })->limit(($pagenum - 1) * $pagesize, $pagesize)->select()->toArray();
            } else {
                $where = [
                    'title' => $query,
                ];
                $res = $this->hasWhere('productCate', function ($query) use ($cate_id) {
                    $query->where('family', 'like', '%' . $cate_id . ',%');
                })->where($where)->limit(($pagenum - 1) * $pagesize, $pagesize)->select()->toArray();
            }
        }

        return $res;
    }

    public function getTotal($cate_id, $query = null)
    {
        if (empty($query)) {
            $where = [
                'cate_id' => $cate_id
            ];
            $res = $this->where($where)->select()->count();
        } else {
            $where = [
                'cate_id' => $cate_id,
                'title' => $query
            ];
            $res = $this->where($where)->select()->count();
        }
        return $res;
    }

    public function getRecommend($cate_id, $limit)
    {
        if ($cate_id == 'all') {
            $res = $this->limit($limit)->select();
        } else {
            $where = [
                'cate_id' => $cate_id,
                'family' => [
                    'like',
                    '%' . $cate_id . ',%',
                ]
            ];
            $res = $this->where($where)->limit($limit)->select();
        }
        return $res;
    }

    public function updateStatusById($id, $status)
    {
        if (empty($id)) {
            return false;
        }

        $where = [
            'id' => $id
        ];

        $pro = $this->where($where)->find();
        $pro->status = $status;
        $res = $pro->save();
        return $res;
    }


    public function updateById($id, $from)
    {
        if (empty($id)) {
            return false;
        }

        $where = [
            'id' => $id
        ];

        $res = $this->where($where)->save($from);
        return $res;
    }

    public function getProBySku($skuId)
    {
        if (empty($skuId)) {
            return false;
        }
        $res = $this->hasWhere('Sku',['id'=>$skuId])->find()->toArray();
        return $res;
    }
}
