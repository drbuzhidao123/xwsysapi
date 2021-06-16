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

    public function updateById($userid,$data)
    {
        if(empty($userid)){
         return false;
        }

        $where = [
            'id' => $userid
        ];
        $user = $this->where($where)->find();
        
        if(!empty($data['password'])){
        $user->password = \passwordMd5($data['password']);
        }
        $user->username = $data['username'];
        $user->mobile = $data['mobile'];
        $user->address = $data['address']; 
        $res = $user->save();
        return $res;
        
    }

    

}