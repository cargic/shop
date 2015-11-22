<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/11/20
 * Time: 9:35
 */

namespace Home\Behavior;


use Think\Behavior;

class CheckUserBehavior extends Behavior
{
    public function run(&$params){
        //>>1.判定用户如果登陆就定义UID
        if(isLogin()){
            $userinfo = login();
            defined('UID') or define('UID',$userinfo['id']);
        }
    }

}