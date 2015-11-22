<?php
namespace Admin\Model;


use Admin\Service\NestedSetsService;
use Think\Model;

class MenuModel extends BaseModel
{
    // 自动验证定义
    protected $_validate = array(
        array('name','require','菜单名称不能够为空!'),
        array('status','require','状态不能够为空!'),
        array('sort','require','排序不能够为空!'),
    );


    public function add($requestData){
        //>>1.计算左右边界
            $dbMysqlInterfaceImplModel = D('DbMysqlInterfaceImpl');
            $nestedSetService = new NestedSetsService($dbMysqlInterfaceImplModel,'menu','lft','rght','parent_id','id','level');
        //>>2.再进行保存
            $id = $nestedSetService->insert($this->data['parent_id'],$this->data,'bottom');
            if($id===false){
                return false;
            }

        //>>3.需要在权限数据保存到menu_permission表中
            $result = $this->handlePermission($id,$requestData['permission_ids']);
            if($result===false){
                return false;
            }
            return $id;
    }

    /**
     * 将菜单的id 和 权限的id保存到menu_permission表中
     * @param $menu_id
     * @param $permission_ids
     * @return bool
     */
    private function handlePermission($menu_id,$permission_ids){
        $rows = array();
         foreach($permission_ids as $permission_id){
                $rows[] = array('permission_id'=>$permission_id,'menu_id'=>$menu_id);
         }
        $menuPermissionModel = M('MenuPermission');
        $menuPermissionModel->where(array('menu_id'=>$menu_id))->delete();
        if(!empty($rows)){
            $result = $menuPermissionModel->addAll($rows);
            if($result===false){
                $this->error = '保存权限失败!';
                return false;
            }
        }

    }

    public function getList($field='id,name,parent_id'){
        return $this->where('status>=0')->field($field)->order('lft')->select();
    }

    public  function save($requestData){
        //>>1.计算左右边界
        $dbMysqlInterfaceImplModel = D('DbMysqlInterfaceImpl');
        $nestedSetService = new NestedSetsService($dbMysqlInterfaceImplModel,'menu','lft','rght','parent_id','id','level');

         //根据当前id查询出父权限的id(数据库中的id)  和  请求中的父权限的id进行对应

        //>>2.进行移动
        $result = $nestedSetService->moveUnder($this->data['id'],$this->data['parent_id']);
        if($result===false){
            $this->error = '移动菜单失败!';
            return false;
        }
        //>>3.将原来的删除,现在的添加进去
        $result = $this->handlePermission($requestData['id'],$requestData['permission_ids']);
        if($result===false){
            return false;
        }

        return parent::save($this->data);
    }


    /**
     * 根据菜单单id查找到该菜单的权限
     * @param $menu_id
     * @return array
     */
    public function getPermissionIdByMenuId($menu_id){
        $sql = "select permission_id from menu_permission where menu_id = $menu_id";
        $rows = $this->query($sql);
        return array_column($rows,'permission_id');
    }
}