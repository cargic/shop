<?php
/**
 * Created by PhpStorm.
 * User: aa
 * Date: 2015/11/21
 * Time: 22:40
 */

namespace Home\Controller;


use Think\Controller;

class AddressController extends Controller
{

    public function _initialize(){
        if(!isLogin()){
            cookie('__LOGIN_RETURN_URL__',$_SERVER['REQUEST_URI']);
            $this->error('请登录!',U('Member/login'));
        }
    }
    public function index(){
        //>>1. 准备所有的省份数据
        $regionModel = D('Region');
        $regions = $regionModel->getChildren();
        $this->assign('regions',$regions);
        //>>2. 准备当前用户的收货地址
        $addressModel = D('Address');
        $addresses = $addressModel->getList();
        $this->assign('addresses',$addresses);
        $this->display('index');
    }

    public function add(){
        $addressModel = D('Address');
        if($data = $addressModel->create()){
            if(empty($data['id'])){
                //>>1.保存
                if(($addressModel->add())!==false){
                    $this->success('添加成功!',U('index'));
                }
            }else{
                if(($addressModel->save())!==false){
                    $this->success('编辑成功!',U('index'));
                }
            }
        }
    }

    public function remove($id){
        $addressModel = D('Address');
        $result = $addressModel->delete($id);
        if($result!==false){
            $this->success('删除成功!');
        }else{
            $this->error('删除失败!');
        }
    }

    public function edit($id){
        $addressModel = D('Address');
        $address = $addressModel->find($id);
        $this->ajaxReturn($address);
    }
}