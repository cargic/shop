<?php
/**
 * Created by PhpStorm.
 * User: aa
 * Date: 2015/11/16
 * Time: 17:04
 */

namespace Home\Model;

use Org\Util\String;
use Think\Model;

class MemberModel extends Model
{
    /**
     * 完成密码的自动加密和时间的自动完成
     * @var array
     */
    protected $_auto=array(
        array('salt','\Org\Util\String::randString','','function'),
        array('mail_key','generateKey','','callback'),
        array('status',-1),  //默认为-1, 表示没有激活
        array('add_time',NOW_TIME),
    );

    /**
     * 随机生成一个key值
     * @return string
     */
    public function generateKey(){
        return md5(String::randString(10));
    }

    public function login(){
        $username = $this->data['username'];
        $password = $this->data['password'];
//        动态查询
        $row = $this->getByUsername($username);
        if($row){
//            if($row['status']!=1) {
//                $this->error = ' , 账号未被激活或者被禁用';
//                return false;
//            }
            if(md5($password.$row['salt']) == $row['password']){
                //如果用户登录成功,将cookie中的数据保存到数据库中
                defined('UID') or define('UID',$row['id']);
                $shoppingCarModel = D('ShoppingCar');
                $shoppingCarModel->cookie2DB();
                return $row;
            }else{
                $this->error =' , 密码错误,请重新输入';
                return false;
            }
        }else{
            $this->error =' , 用户名错误';
            return false;
        }
    }
    /**
     * 根据参数检查是否重复
     * @param $params
     * @return  如果没有返回true
     */
    public function checkRepeat($params)
    {
        $row = $this->where($params)->find();
        return empty($row);
    }

    /**
     * 发送短信
     * @param $tel
     * @return bool
     */
    public function sendSMS($tel){
        //>>1. 随机生成一个数字
        $randomNumber = String::randString(6,1);
        session('SMS_CODE',$randomNumber);  //为了和用户输入的短信验证码进行验证码
        //>>2.将该数字发送到$tel手机号
        vendor('SMS.TopSdk');
        date_default_timezone_set('Asia/Shanghai');  //设置时区

        $c = new \TopClient;
        $c->appkey = '23269002';                              //创建的应用上的appkey
        $c->secretKey = '5b715f15faf1d316c6370897876f1aea';   //创建的应用上的secretKey

        $req = new \AlibabaAliqinFcSmsNumSendRequest;
//        $req->setExtend("123456");
        $req->setSmsType("normal");  //固定的
        $req->setSmsFreeSignName("注册验证");   //你发送的短信是干什么的?
        $req->setSmsTemplateCode("SMS_2235333");  //模板的id
        $req->setSmsParam("{'code':'$randomNumber','product':'[马熙坤基站]的忠实'}");
        $req->setRecNum($tel);  //发送的手机号
        $resp = $c->execute($req);
        //判定发送的状态
        return ((string)$resp->result->success)==='true';
    }

    /**
     * 用户信息的添加
     * @return mixed
     */
    public function add(){
        //>>1.写保存注册用户
        $this->data['password'] = md5($this->data['password'].$this->data['salt']);
        $mail = $this->data['email'];  //发送到的email
        $mail_key = $this->data['mail_key'];  //需要验证的key
        $id = parent::add();


        //>>2.发送激活邮件
//        $content = "<a href='http://www.shop.com/index.php/Member/fire/id/$id/key/{$mail_key}'>请点击这里激活你的账号</a>";
//        $result = $this->sendMail($mail,'京西的激活邮件',$content);
//        if($result===false){
//            $this->error  = '请重新填写Email发送邮件';
//            return false;
//        }
        return $id;                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                     ;
    }
    private function sendMail($mail_str,$title,$content){
        vendor('PHPMailer.PHPMailerAutoload');
        $mail_config = C('MAIL_CONFIG');
        $mail = new \PHPMailer;
        $mail->isSMTP();                                      // 设置发送邮件协议: SMTP
        $mail->Host = $mail_config['Host'];                         // 设置邮件的服务器
        $mail->SMTPAuth = true;                               // 开启授权
        $mail->Username = $mail_config['Username'];              // 登陆用户的用户名
        $mail->Password = $mail_config['Password'];                    // 登陆用户的密码

        /////////////////////准备邮件内容///////////////////////////////////////
        $mail->setFrom($mail_config['From'], 'NOReply');          //发件人

        $mail->addAddress($mail_str);     // 收件人

        $mail->isHTML(true);                                  // 设置邮件为Html的邮件
        $mail->CharSet = 'utf-8';                              //设置编码
        $mail->Subject = $title;   //邮件的标题
        $mail->Body = $content;   //邮件的内容
        if ($mail->send() === false) {
            dump($mail->ErrorInfo);exit;
            $this->error = $mail->ErrorInfo;
            return false;
        }
    }

    /**
     * 邮箱激活账号
     * @param $id
     * @param $key
     * @return bool
     */
    public function fire($id,$key){
        //>>1.根据id找到数据表中的$key
        $mail_key  = $this->getFieldById($id,'mail_key');
        //>>2.验证key
        if($key==$mail_key){
            //>>3.修改状态
            return $this->where(array('id'=>$id))->setField('status',1);
        }else{
            return false;
        }
    }
}