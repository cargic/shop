<?php
/**
 * Created by PhpStorm.
 * User: aa
 * Date: 2015/11/15
 * Time: 22:39
 */

namespace Home\Controller;


use Think\Controller;

class MemberController extends Controller
{
    /**
     * 用户登录
     */
    public function login(){
        if(IS_POST){
            $memberModel = D('Member');
            if($memberModel->create()!==false){
                $result = $memberModel->login();
                if(is_array($result)){
                    login($result);
                    //从cookie中获取到url
                    $login_return_url = cookie('__LOGIN_RETURN_URL__');
                    if(empty($login_return_url)){
                        $login_return_url =  U('Index/index');
                    }else{
                        cookie('__LOGIN_RETURN_URL__',null);
                    }
                    $this->success('登录成功',$login_return_url);
                }else{
                    $this->error('登录失败'.$memberModel->getError());
                }
            }
        }else{
            $this->display();
        }
    }

    /**
     * 账户注销
     */
    public function logout(){
        logout();
        $this->success('注销成功!',U('Index/index'));
    }
    /**
     * 用户注册
     */
    public function regist(){
        if(IS_POST){
            //>>1.注册之前先对短信验证码进行验证.
            $checkCode = I('post.checkcode');  //请求中用户输入的验证码
            $sms_code = session('SMS_CODE');
            if($checkCode!==$sms_code){
                $this->error('短信验证码错误!');
            }else{
                session('SMS_CODE',null);  //短信验证成功之后删除保存在session中短信内容
            }

            $memberModel = D('Member');
            if($memberModel->create()!==false){
                if($memberModel->add()!==false){
                    $this->success('注册成功',U('login'));
                    return;
                }
            }
            $this->error('注册失败!'.$memberModel->getError());
        }else{
            $this->display('regist');
        }
    }
    /**
     * 验证数据是否重复
     */
    public function check(){
        //>>1.获取请求参数
        $params = I("get.");
        //>>2.让模型进行验证
        $memberModel=  D('Member');
        /**
         * result的值一定要是true或者false
         */
        $result = $memberModel->checkRepeat($params);
        //>>3.验证的结果
        $this->ajaxReturn($result);
    }
    /**
     * 发送验证码给当前手机号码
     * @param $tel
     */
    public function sendSMS($tel){
        $memberModel = D('Member');
        //发送短信的结果: true或者false
        $result = $memberModel->sendSMS($tel);
        $this->ajaxReturn($result);
    }

    /**
     * 邮件激活账号
     * @param $id
     * @param $key
     */
    public function fire($id,$key){
        $memberModel = D('Member');
        $result = $memberModel->fire($id,$key);
        if($result===false){
            $this->error('激活失败!,重新激活');
        }else{
            $this->success('激活成功!',U('login'));
        }
    }
}