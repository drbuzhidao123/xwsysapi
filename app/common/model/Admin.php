<?php
namespace app\common\model;

use think\Model;
use think\facade\Db;

class Admin extends Model
{

    public function AuthGroupAccess()
{
    return $this->hasOne(AuthGroupAccess::class,'uid');
}

    public function getUserById($id)
    {
        if(empty($id)){
             return false;
        }

        $where = [
            'id' => $id
        ];

        $res = $this->where($where)->find();

        return $res;
       
    }

    public function getUserByusername($username)
    {
        if(empty($username)){
             return false;
        }

        $where = [
            'username' => $username
        ];

        $res = $this->where($where)->find();

        return $res;
       
    }

    public function updateUserByusername($username,$info){
        if(empty($username)){
            return false;
       }

       $where = [
        'username' => $username
    ];

       $res = $this->where($where)->update($info);
       if($res){
           $res = $this->where($where)->find();
       }else{
           return false;
       }
       return $res;
    }

    //获取数据列表
    public function getUserList($pagenum,$pagesize,$query)
    {
        if(empty($pagenum)&&empty($pagesize)){
             return false;
        }
        if(empty($query)){
            $res = Db::table('admin')->alias('u')
        ->leftJoin('auth_group_access a','u.id = a.uid')
        ->leftJoin('auth_group g','a.group_id = g.id')
        ->field('u.id,u.username,u.password,u.status,u.created,u.updated,u.logined,u.token,u.token_out,u.mobile,a.group_id,g.title')
        ->limit(($pagenum-1)*$pagesize,$pagesize)
        ->field('')
        ->select();
        //$res = $this->limit(($pagenum-1)*$pagesize,$pagesize)->select();
        }else{
            $where=[
                'username'=>$query
            ];
            $res = Db::table('admin')->alias('u')
            ->where($where)
            ->leftJoin('auth_group_access a','u.id = a.uid')
            ->leftJoin('auth_group g','a.group_id = g.id')
            ->field('u.id,u.username,u.password,u.status,u.created,u.updated,u.logined,u.token,u.token_out,u.mobile,a.group_id,g.title')
            ->limit(($pagenum-1)*$pagesize,$pagesize)
            ->select();
        //$res = $this->where($where)->limit(($pagenum-1)*$pagesize,$pagesize)->select();
        };
        return $res;
    }



    public function getUserTotal($query)
    { 
        if(empty($query)){
            $res = $this->select()->count();
            }else{
                $where=[
                    'username'=>$query
                ];
            $res = $this->where($where)->select()->count();
            }
        return $res;
    }

    public function updateStatusById($userid,$status)
    {
        if(empty($userid)){
         return false;
        }

        $where = [
            'id' => $userid
        ];

        $user = $this->where($where)->find();
        $user->status = $status; 
        $res = $user->save();
        return $res;
        
    }

    public function updateById($userid,$user)
    {
        if(empty($userid)){
         return false;
        }

        $where = [
            'id' => $userid
        ];
        $admin=$this->where($where)->find();
        if($user['password']==''){ 
           $admin->username=$user['username'];
           $admin->updated=date("Y-m-d h:i:s",time());
           $admin->mobile=$user['mobile'];
        }else{
            $admin->username=$user['username'];
            $admin->password=\passwordMd5($user['password']);
            $admin->updated=date("Y-m-d h:i:s",time());
            $admin->mobile=$user['mobile'];
        }

        $res = $admin->save();
        return $res;
        
    }

    

    /*public function add($userform)
    {
        if(empty($userform)){
         return false;
        }

        $res = $this->create($userform);
        return $res;
        
    }*/

    

}