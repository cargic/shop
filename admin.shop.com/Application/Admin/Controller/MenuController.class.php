<?php
namespace Admin\Controller;

use Think\Controller;

class MenuController extends BaseController
{
    protected $meta_title = '菜单';

    protected $usePostAllParams = true;

    public function index(){
        $rows = $this->model->getList("id,name,parent_id,status,level,sort,url,intro");
        $this->assign('rows',$rows);
        $this->assign('meta_title',$this->meta_title);
        //将当前列表的url保存到cookie中,为了 删除,添加,修改状态,编辑 之后跳转到该地址
        cookie('__forward__', $_SERVER['REQUEST_URI']);

        $this->display('index');
    }


    public function _before_edit_view(){
        //>>1.准备页面上的树需要的数据
         $rows = $this->model->getList();
         $this->assign('nodes',json_encode($rows));

        //>>2.准备所有的权限数据
         $permissionModel = D('Permission');
         $permissiones = $permissionModel->getList();
         $this->assign('permissions',json_encode($permissiones));

         $id = I('get.id');
         if(!empty($id)){
             //说明是编辑
             $permission_ids  =  $this->model->getPermissionIdByMenuId($id);
             $this->assign('permission_ids',json_encode($permission_ids));

         }

    }
}