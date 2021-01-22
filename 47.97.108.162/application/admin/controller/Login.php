<?php

/**
 *  登陆页
 * @file   Login.php  
 * @date   2016-8-23 19:52:46 
 * @author Zhenxun Du<5552123@qq.com>  
 * @version    SVN:$Id:$ 
 */

namespace app\admin\controller;

use think\Controller;
use think\Loader;
use think\Session;
class Login extends Controller {

    /**
     * 登入
     */
    public function index() {
        //自动登录
        if(Session::has('username')){
            $this->success('登入成功', 'main/index');
        }
        else{
            return $this->fetch();
        }
    }
    /**
     * 处理登录
     */
    public function logup() {
            return $this->fetch();
    }
    
    public function dologin() {
        //验证密码流程
        
        //假设用户名密码正确，密码采用md5加密
        $username = input('post.username');
        $password = input('post.password');
        $info = db('userinfo')->field('username,password,userrole')->where('username', $username)->find();
        if (!$info) {
            $this->error('用户名或密码错误');
        }

        if (md5($password) != $info['password']) {
            # echo md5($password);
            $this->error('用户名或密码错误');
        }
        else {
            Session::set('username', $info['username']);
            Session::set('userrole', $info['userrole']);
            
             //记录登录信息 用户名和权限组
            
            $this->success('登入成功', 'main/index');
        }
    }
    
    public function dologup() {
        //用户注册
        
        //判断两次输入密码是否相同
        $username = input('post.username');
        $password = input('post.password');
        $rpassword = input('post.rpassword');
        if($password != $rpassword){
            $this->error('两次输入密码不一致！');
        }
        
        //查询用户名是否已存在
        $info = db('userinfo')->field('username,password')->where('username', $username)->find();
        if ($info) {
            $this->error('用户名已存在');
        }
        
        //插入该用户数据，默认为student权限组
        $data = ['username' => $username, 'password' => md5($password)];
        db('userinfo')->insert($data);
            $this->success('注册成功', 'login/index');
    }

    /**
     * 登出
     */
    public function logout() {
        // 清空session信息
        Session::set('username', null);
        Session::set('username', null);
        $this->success('退出成功', 'login/index');
    }
    
    public function test(){
        echo 'test';
    }

}
