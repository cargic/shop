<?php
namespace Admin\Model;


use Think\Model;
use Think\Page;

class RoleModel extends BaseModel
{
    protected $_validate = array(
        array('name','require','角色名称不能够为空!'),
        array('status','require','状态不能够为空!'),
        array('sort','require','排序不能够为空!'),
    );

    /**
     * 实现角色的添加功能
     * @param mixed|string $requestData
     * @return bool|mixed
     */
    public function add($requestData) {
        //>>1.将请求中的数据保存到role表中
        $role_id = parent::add();
        if($role_id===false){
            return false;
        }
        //>>2.将用户选中的权限保存到role_permission的关系表中
        $result = $this->handlePermission($role_id,$requestData['permission_ids']);
        if($result===false){
            return false;
        }
        return  $role_id;
    }

    /**
     * 数据的更新
     * @param mixed|string $requestData
     * @return bool
     */
    public function save($requestData){
        //>>1.需要将this->data中的数据更新到role表中
        $result = parent::save();
        if($result===false){
            return false;
        }
        //>>2.需要将请求中的权限数据更新到中间表(原来的删除, 现在的添加进去)
        $result1 = $this->handlePermission($requestData['id'],$requestData['permission_ids']);
        if($result1===false){
            echo 11;
            return false;
        }
        return $result;
    }
    /**
     * 处理角色和权限之间的关系,并实现保存到role_permisssion表中
     * @param $role_id
     * @param $permission_ids
     * @return bool
     */
    private function handlePermission($role_id,$permission_ids){
        $rows = array();
        foreach($permission_ids as $permission_id){
            $rows[] = array('role_id'=>$role_id,'permission_id'=>$permission_id);
        }
        $rolePermissionModel = M('RolePermission');
        $rolePermissionModel->where(array('role_id'=>$role_id))->delete();
        if(!empty($rows)){
            $result = $rolePermissionModel->addAll($rows);
            if($result===false){
                $this->error = '保存权限失败!';
                return false;
            }
        }
    }

    /**
     * 根据role_id查询角色所拥有的权限
     * @param $role_id
     * @return array
     */
    public function getPermissionIdByRoleId($role_id){
        $sql  = "select permission_id from role_permission where role_id=".$role_id;
        $rows = $this->query($sql);
        $permission_ids = array_column($rows,'permission_id');
        return $permission_ids;
    }
}