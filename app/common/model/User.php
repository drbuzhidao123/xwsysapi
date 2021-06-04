<?php
namespace app\common\model;

use think\Model;

class User extends Model
{



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

    

}