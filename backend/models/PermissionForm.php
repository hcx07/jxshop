<?php
namespace backend\models;

use yii\base\Model;
use yii\rbac\Permission;

class PermissionForm extends Model{
    public $name;
    public $description;
    public function rules()
    {
        return [
            [['name','description'],'required'],
        ];
    }
    public function attributeLabels()
    {
        return [
            'name'=>'名称',
            'description'=>'描述',
        ];
    }
    //添加权限方法
    public function addPermission(){
        $authManager=\Yii::$app->authManager;
        if($authManager->getPermission($this->name)){
            $this->addError('name','权限已存在');
        }else{
            $permission=$authManager->createPermission($this->name);
            $permission->description=$this->description;
            return $authManager->add($permission);
        }
        return false;
    }
    //从权限中加载数据
    public function loadDate(Permission $permission){
        $this->name=$permission->name;
        $this->description=$permission->description;
    }
    //修改权限
    public function updatePermission($name){
        $authManager=\Yii::$app->authManager;
        $permission=$authManager->getPermission($name);
        //判断修改后的权限是否存在  如果修改了名字并且修改后的名字已存在则返回false
        if($name!=$this->name && $authManager->getPermission($this->name)){
            $this->addError('name','权限已存在');
        }else{
            $permission->name=$this->name;
            $permission->description=$this->description;
            return $authManager->update($name,$permission);
        }
        return false;
    }

}
