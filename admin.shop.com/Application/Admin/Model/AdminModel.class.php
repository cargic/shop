<?php
namespace Admin\Model;


use Think\Model;
use Think\Page;

class AdminModel extends BaseModel
{
    protected $_validate = array(
        array('username','require','用户名不能够为空!'),
        array('username','','用户名已经存在!','','unique'),
        array('password','require','密码不能够为空!'),
        array('email','require','Email不能够为空!'),
        array('email','require','Email已经存在!','','unique'),
        array('add_time','require','加入时间不能够为空!'),
        array('last_login_time','require','最后登录时间不能够为空!'),
        array('salt','require','盐不能够为空!'),
        array('status','require','状态不能够为空!'),
        array('sort','require','排序不能够为空!'),
    );
    // 自动完成定义
    protected $_auto= array(
        array('add_time',NOW_TIME),
        array('salt','\Org\Util\String::randString','','function'),
    );

    public function add($requestData){
        //>>1.this->data是请求中的数据,并且已经处理过了
        $this->data['password'] = md5($this->data['password'].$this->data['salt']);
        $id = parent::add();
        if($id===false){
            return false;
        }
        //>>2.需要将请求中的角色和用户的关系添加到admin_role中
        $result = $this->handleRole($id,$requestData['role_ids']);
        if($result===false){
            return false;
        }
        //>>3.需要将请求中的用户和额外权限添加到 admin_permission表中
        $result = $this->handlePermission($id,$requestData['permission_ids']);
        if($result===false){
            return false;
        }
        return $id;
    }

    /**
     * @param mixed|string $requestData
     * @return bool
     */
    public function save($requestData){
        //>>1.先将请求中的数据更新到admin表中
        $result = parent::save();
        if($result===false){
            return false;
        }
        //>>2.再将请求中的角色数据更新到中间表中
        $result1 =$this->handleRole($requestData['id'],$requestData['role_ids']);
        if($result1===false){
            return false;
        }
        //>>3.再将请求中的额外权限数据更新到中间表admin_permission中
        $result2 = $this->handlePermission($requestData['id'],$requestData['permission_ids']);
        if($result2===false){
            return false;
        }
        return $result;
    }

    /**
     * 将请求中的角色id和当前用户保存到admin_role表中
     * @param $admin_id
     * @param $role_ids
     * @return bool
     */
    private  function handleRole($admin_id,$role_ids){
        $rows = array();
        foreach($role_ids as $role_id){
            $rows[] = array('admin_id'=>$admin_id,'role_id'=>$role_id);
        }
        $adminRoleModel = M('AdminRole');
        $adminRoleModel->where(array('admin_id'=>$admin_id))->delete();
        if(!empty($rows)){
            $result= $adminRoleModel->addAll($rows);
            if($result===false){
                return false;
            }
        }
    }

    /**
     * 将管理员和权限添加到额外权限表中  admin_permission
     * @param $admin_id
     * @param $permission_ids
     * @return bool
     */
    private function handlePermission($admin_id,$permission_ids){
        $rows  = array();
        foreach($permission_ids as $permission_id){
            $rows[] = array('permission_id'=>$permission_id,'admin_id'=>$admin_id);
        }
        $adminPermissionModel = M('AdminPermission');
        $adminPermissionModel->where(array('admin_id'=>$admin_id))->delete();
        if(!empty($rows)){
            $result = $adminPermissionModel->addAll($rows);
            if($result===false){
                $this->error  = '添加额外权限失败!';
                return false;
            }
        }
    }

    /**
     * 重置密码为aaaaaa
     * @param $id
     * @return bool
     */
    public function initPassword($id){
        //>>1.根据id查询出盐
        $salt = $this->getFieldById($id,'salt');
        //>>2.再将密码和盐进行加密
        $password = md5('aaaaaa'.$salt);
        //>>3.加密后的结果更新到数据库表中
        return parent::save(array('id'=>$id,'password'=>$password));
    }

    /**
     * 根据管理员的id找到对应的角色id
     * @param $admin_id
     * @return array
     */
    public function getRoleIdByAdminId($admin_id){
        $sql = "select role_id from admin_role where admin_id = ".$admin_id;
        $rows = $this->query($sql);
        return array_column($rows,'role_id');
    }

    /**
     * 根据管理员的id从admin_permission表中查询
     * @param $admin_id
     * @return array
     */
    public function getPermissionIdByAdminId($admin_id){
        $sql = "select permission_id  from admin_permission where admin_id = ".$admin_id;
        $rows = $this->query($sql);
        return array_column($rows,'permission_id');
    }
}