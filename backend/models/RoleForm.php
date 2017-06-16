<?php
namespace backend\models;

use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\rbac\Role;

class RoleForm extends Model{
    public $name;//角色名
    public $description;//角色描述
    public $permissions=[];//角色的权限
    public function rules()
    {
        return [
            [['name','description'],'required'],
            [['permissions'],'safe'],//字段不需要验证
        ];
    }
    public function attributeLabels()
    {
        return [
            'name'=>'名称',
            'description'=>'描述',
            'permissions'=>'权限'
        ];
    }
    public static function getPermissionOptions(){
        return ArrayHelper::map(\Yii::$app->authManager->getPermissions(),'name','description');
    }
    public function addRole(){
        $authManager=\Yii::$app->authManager;
        if($authManager->getRole($this->name)){
            $this->addError('name','角色已存在');
        }else{
            $role=$authManager->createRole($this->name);
            $role->description=$this->description;
            if($authManager->add($role)){//将角色保存到数据表
//                var_dump($this->permissions);exit;
                foreach ($this->permissions as $permissionName){
                    $permission=$authManager->getPermission($permissionName);
                    if($permission){
                        $authManager->addChild($role,$permission);
                    }
                }
                return true;
            }
        }
        return false;
    }
    public function loadDate(Role $role){
//        var_dump($role);exit;
        $this->name=$role->name;
        $this->description=$role->description;
        //通过角色获取到权限
        $permissions=\Yii::$app->authManager->getPermissionsByRole($role->name);
        foreach ($permissions as $permission){
            $this->permissions[]=$permission;
        }
    }
    public function updateRole($name){
        $authManager=\Yii::$app->authManager;
        $role = $authManager->getRole($name);
        $role->name=$this->name;
        $role->description=$this->description;
        //如果角色名修改且修改后的名字已存在，则给出错误提示
        if($name!=$this->name && $authManager->getRole($this->name)){
           $this->addError('name','角色名重复');
        }else{
            //如果成功修改了角色权限  去掉该角色所有的权限然后再重新添加权限
            if($authManager->update($name,$role)){
                $authManager->removeChildren($role);
                //添加权限
                if($this->permissions){
                    foreach ($this->permissions as $permissionName){
                        $permission=$authManager->getPermission($permissionName);
                        if($permission){
                            $authManager->addChild($role,$permission);
                        }
                    }
                }
            }
            return true;
        }
        return false;
    }
}
