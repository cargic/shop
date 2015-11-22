<?php
namespace Admin\Controller;

use Think\Controller;

class IndexController extends Controller{

    public function menu()
    {
        //>>1.准备所有的菜单数据
        if(isSuperUser()){
            //>>2.如果是超级管理员,查询所有的菜单
            $menuModel = D('Menu');
            $menus = $menuModel->getList('id,name,url,parent_id,level');
        }else{
            //>>2.如果不是超级管理员, 根据权限查询菜单
            $permission_ids = savePermissionId();
            if($permission_ids){
                $permission_ids = arr2str($permission_ids);
                $sql = "select distinct m.id,m.name,m.url,m.level,m.parent_id from menu as m join menu_permission as mp on m.id = mp.menu_id  where mp.permission_id in ($permission_ids)";
                $menus = M()->query($sql);
            }
        }
        $this->assign('menus',$menus);
        $this->display('menu');
    }
}