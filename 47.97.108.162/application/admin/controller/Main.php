<?php

/**
 *  登陆页
 * @file    
 * @date    
 * @author  
 * @version    
 */

namespace app\admin\controller;



use think\Controller;
use think\Loader;
use think\Db;
use think\Session;
class Main extends Controller {
 
    /**
     * 主页面
     */
    public function index() {
        // 获取当前用户名和权限组
        $userinfo["username"] = Session::get('username');
        $userinfo["userrole"] = Session::get('userrole');
        if (!$userinfo) $this->error("还未登录");
        # var_dump($userinfo);
        # return 0;
        $this->assign('userinfo', $userinfo);
        return $this->fetch();
        
    }
    public function fenpeisushe() {
        $username=Session::get('username');
        if (!$username) $this->error("还未登录");
        $this->assign('username',$username);
        $info = db('userinfo')->field('userrole')->where('username', $username)->find();
        $role=$info['userrole'];
        $info_1 = db('roleaccess')->where('access', 'fenpeisushe')->find();
        if ($info_1[$role]==0){
           ?> 
           <script type="text/javascript">
               alert("您无权限进行该操作！");
               window.location.href="./index";
           </script>

           <?php 
        }
        else{
            return $this->fetch();
        }
        
        
    }
    public function fenpei_sushe(){
        $username = input('post.username');
        $room=input('post.room');
        $info = db('userinfo')->field('username','room')->where('username', $username)->find();
        if ($info['room']!=0){
            $this->error('此用户已有宿舍','main/tongjichaxun');
        }
        $room_occupied_1= db('building')->field('occupied')->where('room', $room)->find();
        if (!$info) {
            $this->error('用户不存在','main/tongjichaxun');
        }
        if (!$room_occupied_1){
            $this->error('房间不存在','main/tongjichaxun');
        }else{
             $room_full=4;
        $room_occupied=$room_occupied_1['occupied'];
        }
       
        if ($room_occupied==$room_full){
            $this->error('房间已满','main/tongjichaxun');
        }
        $res = Db::name('userinfo')->where('username',$username)->update(['room'=>$room]);
        $res = Db::name('building')->where('room',$room)->update(['occupied'=>++$room_occupied]);
        $this->success('分配宿舍成功', 'main/tongjichaxun');
        
    }
    public function tuisu() {
        $username=Session::get('username');
        if (!$username) $this->error("还未登录");
        $res= db('userinfo')->where('username',$username)->find();
        if ($res["room"] == 0) $this->error("您当前尚未分配宿舍，无法退宿");
        $this->assign('username',$username);
        $this->assign('room', $res["room"]);
        return $this->fetch();
    }
    public function tui_su() {
        $username=Session::get('username');
        if (!$username) $this->error("还未登录");
        $this->assign('username',$username);
        $lists = db('userinfo')->where('username', $username)->find();
        $res = db('building')->where('room',$lists["room"])->find();
        $noccupied = $res["occupied"]-1;
        db('building')->where('room',$lists["room"])->update(['occupied'=>$noccupied]);
        db('userinfo')->where('username', $username)->update(['room'=>'0']);
        $this->success('退宿操作成功', 'main/index');
        //var_dump($lists);
        //echo count($lists);
        //echo $lists['username'];
        return ;
        
    }
    public function del_sushe(){
        $username = input('post.username');
        $info = db('userinfo')->field('username','room')->where('username', $username)->find();
        $room_occupied_1= db('building')->field('occupied')->where('room',$info['room'])->find();
        if (!$info) {
            $this->error('用户不存在','main/tongjichaxun');
        }
        $res = Db::name('userinfo')->where('username',$username)->update(['room'=>'0']);
        $room_occupied=$room_occupied_1['occupied'];
        if ($room_occupied!=0){
            --$room_occupied;
            $res = Db::name('building')->where('room',$info['room'])->update(['occupied'=>$room_occupied]);
        }
        else{
            $this->error('该用户未被分配宿舍','main/tongjichaxun');
        }
        $this->success('退宿成功', 'main/tongjichaxun');
    }
    public function diaohuansushe() {
        $username=Session::get('username');
        if (!$username) $this->error("还未登录");
        $this->assign('username',$username);
        $info = db('userinfo')->field('userrole')->where('username', $username)->find();
        $role=$info['userrole'];
        $info_1 = db('roleaccess')->where('access', 'diaohuansushe')->find();
        if ($info_1[$role]==0){
           ?> 
           <script type="text/javascript">
               alert("您无权限进行该操作！");
               window.location.href="./index";
           </script>
           <?php 
        }
        return $this->fetch();
        
    }
    public function diaohuan_sushe(){
        $username = input('post.username');
        $room_yuan=input('post.room_yuan');
        $room=input('post.room');
        $info = db('userinfo')->field('username','room')->where('username', $username)->find();

        $room_occupied_1= db('building')->field('occupied')->where('room', $room)->find();
        $room_occupied_2= db('building')->field('occupied')->where('room', $room_yuan)->find();
        if (!$info) {
            $this->error('用户不存在','main/tongjichaxun');
        }
        if ($info['room']!=$room_yuan){
            $this->error('原房间号错误','main/tongjichaxun');
        }
        if (!$room_occupied_1){
            $this->error('欲调换房间不存在','main/tongjichaxun');
        }else{
             $room_full=4;
             
             $room_occupied=$room_occupied_1['occupied'];
             $room_occupied_yuan=$room_occupied_2['occupied'];
        }
        if ($room_occupied==$room_full){
            $this->error('欲调换房间已满','main/tongjichaxun');
        }
        $res = Db::name('userinfo')->where('username',$username)->update(['room'=>$room]);
        $res = Db::name('building')->where('room',$room)->update(['occupied'=>++$room_occupied]);
        $res = Db::name('building')->where('room',$room_yuan)->update(['occupied'=>--$room_occupied_yuan]);
        $this->success('调换宿舍成功', 'main/tongjichaxun');
        
    }
    public function tongjichaxun() {
        $username=Session::get('username');
        if (!$username) $this->error("还未登录");
        $this->assign('username',$username);
        $info = db('userinfo')->field('userrole')->where('username', $username)->find();
        $role=$info['userrole'];
        $info_1 = db('roleaccess')->where('access', 'sushetongjichaxun')->find();
        if ($info_1[$role]==0){
           ?> 
           <script type="text/javascript">
               alert("您无权限进行该操作！");
               window.location.href="./index";
           </script>
           <?php 
        }
        $lists = db('userinfo')->select();
        //var_dump($lists);
        $this->assign('lists',$lists);
        return $this->fetch();
    }
    public function gerenxinxi() {
        $username=Session::get('username');
        if (!$username) $this->error("还未登录");
        $this->assign('username', $username);
        $lists = db('userinfo')->where('username', $username)->find();
        // var_dump($lists);
        // return 0;
        $this->assign('lists',$lists);
        return $this->fetch();
        
        
    }
    public function xiugaimima() {
        $username=Session::get('username');
        if (!$username) $this->error("还未登录");
        $this->assign('username',$username);
        return $this->fetch();
        
    }
    public function xiugai_mima() {
        // echo md5(1234);
        // return 0;
        $username=input('post.username');
        $password_old=input('post.password_old');
        $password_new=input('post.password_new');
        $info = db('userinfo')->field('username','password')->where('username', $username)->find();
        if ($username!=Session::get('username')){
            $this->error('您无权限修改其他用户密码');
        }
        if (!$info) {
            $this->error('用户名或密码错误');
        }

        if (md5($password_old) != $info['password']) {
            $this->error('用户名或密码错误');
        } 
        $res = Db::name('userinfo')->where('username',$username)->update(['password'=>md5($password_new)]);
        $this->success('修改密码成功', 'main/index');
    }
    public function sushexinxi() {
        $username=Session::get('username');
        if (!$username) $this->error("还未登录");
        $this->assign('username',$username);
        $info = db('userinfo')->field('userrole')->where('username', $username)->find();
        $role=$info['userrole'];
        $info_1 = db('roleaccess')->where('access', 'sushexinxi')->find();
        if ($info_1[$role]==0){
           ?> 
           <script type="text/javascript">
               alert("您无权限进行该操作！");
               window.location.href="./index";
           </script>
           <?php 
        }
        $lists = db('building')->select();
        //var_dump($lists);
        $this->assign('lists',$lists);
        return $this->fetch();
        
    }
    public function tianjiafangjian() {
        $username=Session::get('username');
        if (!$username) $this->error("还未登录");
        $this->assign('username',$username);
        $info = db('userinfo')->field('userrole')->where('username', $username)->find();
        $role=$info['userrole'];
        $info_1 = db('roleaccess')->where('access', 'tianjiafangjian')->find();
        if ($info_1[$role]==0){
           ?> 
           <script type="text/javascript">
               alert("您无权限进行该操作！");
               window.location.href="./index";
           </script>
           <?php 
        }
        return $this->fetch();
        
    }
    
    public function yonghuxinxi() {
        $username=Session::get('username');
        if (!$username) $this->error("还未登录");
        $this->assign('username', $username);
        $role=Session::get('userrole');
        if ($role != 'superadmin'){
           ?> 
           <script type="text/javascript">
               alert("您无权限进行该操作！");
               window.location.href="./index";
           </script>
           <?php 
        }
        $lists = db('userinfo')->select();
        $this->assign('lists',$lists);
        return $this->fetch();
    }
    
    public function quanxianguanli() {
        $username=Session::get('username');
        if (!$username) $this->error("还未登录");
        $this->assign('username', $username);
        $role=Session::get('userrole');
        if ($role != 'superadmin'){
           ?> 
           <script type="text/javascript">
               alert("您无权限进行该操作！");
               window.location.href="./index";
           </script>
           <?php 
        }
        return $this->fetch();
    }
    
    
    public function shenqingsushe() {
        $username=Session::get('username');
        if (!$username) $this->error("还未登录");
        $this->assign('username', $username);
        $role=Session::get('userrole');
        $lists = db('userinfo')->where('username', $username)->find();
        if($lists['room'] != 0){
            $this->error("您已被分配宿舍，不能再申请宿舍");
        }
        
        return $this->fetch();
    }
    
    public function shenqing_sushe() {
        $username=Session::get('username');
        if (!$username) $this->error("还未登录");
        $this->assign('username', $username);
        $role=Session::get('userrole');
        $room = input('post.room');
        $lists = db('building')->where('room', $room)->find();
        if(!$lists) $this->error("输入房间号不存在");
        if($lists['occupied'] == 4) $this->error("该房间已满，申请失败");
        $occupied = $lists['occupied']+1;
        db('userinfo')->where('username', $username)->update(['room'=>$room]);
        db('building')->where('room', $room)->update(['occupied'=>$occupied]);
        return $this->success("申请成功", 'main/index');
    }
    
    
    
    
    
    public function quanxian_guanli() {
        $username=Session::get('username');
        if (!$username) $this->error("还未登录");
        $this->assign('username', $username);
        $role=Session::get('userrole');
        if ($role != 'superadmin'){
           ?> 
           <script type="text/javascript">
               alert("您无权限进行该操作！");
               window.location.href="./index";
           </script>
           <?php 
        }
        $user = input('post.username');
        $role = input('post.role');
        $info = db('userinfo')->where('username', $user)->find();
        if(!$info) $this->error("用户名不存在");
        if($role == 'student'){
            db('userinfo')->where('username', $user)->update(['userrole' => 'student']);
            return $this->success('设置成功','main/quanxianguanli');
        }
        if($role == 'admin'){
            db('userinfo')->where('username', $user)->update(['userrole' => 'admin']);
            return $this->success('设置成功','main/quanxianguanli');
        }
        return $this->error('设置失败','main/quanxianguanli');
    }
    
    public function tianjia_fangjian() {
        $building=input('post.building');
        $floor=input('post.floor');
        $room=input('post.room');
        $info = db('building')->where('room', $room)->find();
        if (!$info){
            $data = [
                'building' => $building,
                'floor' => $floor,
                'room'=>$room,
            ];
            $res = Db::name('building')->insert($data);
        }
        else {
            $this->error('该房间已存在');
        }
        return $this->success('添加成功','main/sushexinxi');
        
    }
    public function shanchufangjian() {
        $username=Session::get('username');
        if (!$username) $this->error("还未登录");
        $this->assign('username',$username);
        $info = db('userinfo')->field('userrole')->where('username', $username)->find();
        $role=$info['userrole'];
        $info_1 = db('roleaccess')->where('access', 'shanchufangjian')->find();
        if ($info_1[$role]==0){
           ?> 
           <script type="text/javascript">
               alert("您无权限进行该操作！");
               window.location.href="./index";
           </script>
           <?php 
        }
        return $this->fetch();
        
    }
    public function shanchu_fangjian() {
        $building=input('post.building');
        $floor=input('post.floor');
        $room=input('post.room');
        $info = db('building')->where('room', $room)->find();
        if (!$info){
            $this->error('该房间不存在');
        }
        else if($info['occupied']!=0) {
            $this->error('该房间有人居住');
        }
        else {
            $res = Db::name('building')->where('room',$room)->delete();
            return $this->success('删除成功','main/sushexinxi');   
        }
        
        
    }
    public function sushebaoxiu(){
        $username=Session::get('username');
        if (!$username) $this->error("还未登录");
        $this->assign('username',$username);
        return $this->fetch();
    }
    public function sushe_baoxiu(){
        $username=Session::get('username');
        $room=input('post.room');
        $error=input('post.error');
        $info = db('building')->where('room', $room)->find();
        if (!$info){
            $this->error("该房间不存在");
        }
        $data = [
            'room'=>$room,
            'error'=>$error,
            'people'=>$username,
        ];
        $res = Db::name('error')->insert($data);
        return $this->success('成功报修','main/index');  
    }
    public function baoxiuliebiao(){
        $username=Session::get('username');
        if (!$username) $this->error("还未登录");
        $userrole=session::get('userrole');
        $info = db('roleaccess')->where('access', 'baoxiuliebiao')->find();
        if ($info[$userrole]==0){
           ?> 
           <script type="text/javascript">
               alert("您无权限进行该操作！");
               window.location.href="./index";
           </script>
           <?php 
        }
        $lists = db('error')->select();
        
        $this->assign('username',$username);
        //var_dump($lists);
        $this->assign('lists',$lists);
        return $this->fetch();
    }
    public function chulibaoxiu(){
        $username=Session::get('username');
        if (!$username) $this->error("还未登录");
        // $lists = db('error')->select();
        $this->assign('username',$username);
        //var_dump($lists);
        $room = input('post.room');
        $lists = db('error')->where('room', $room)->select();
        if(!$lists) $this->error("房间号输入错误");
        db('error')->where('room', $room)->delete();
        $this->assign('lists',$lists);
        return $this->success('处理成功', 'main/baoxiuliebiao');
    }
    

}?>
