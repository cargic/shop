<?php
namespace Admin\Controller;

use Think\Controller;

class RoleController extends BaseController
{
    protected $meta_title = '角色';
    protected $usePostAllParams = true; //使用请求中的所有数据

    protected function _before_edit_view(){
        //>>1.准备所有的权限数据
        $permissionModel = D('Permission');
        $permissions = $permissionModel->getList();
        $this->assign('nodes',json_encode($permissions));

        $id = I('get.id');;
        if(!empty($id)){
            //当编辑的时候
            //>>1.准备当前角色已经选择的权限
            $permission_ids  = $this->model->getPermissionIdByRoleId($id);
            //>>2.因为页面上需要的是json数据,所以说需要先将改权限转换为json
            $this->assign('permission_ids',json_encode($permission_ids));
        }
    }
}