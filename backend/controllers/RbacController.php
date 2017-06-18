<?php
namespace backend\controllers;

use backend\models\PermissionForm;
use backend\models\RoleForm;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class RbacController extends BackendController {

    //权限列表
    public function actionPermissionIndex(){
        $models=\Yii::$app->authManager->getPermissions();
        return $this->render('permission-index',['models'=>$models]);
    }
    //添加权限
    public function actionAddPermission(){
        $model=new PermissionForm();
        if($model->load(\Yii::$app->request->post()) && $model->validate()){
            if($model->addPermission()){
                \Yii::$app->session->setFlash('权限添加成功！');
                return $this->redirect('permission-index');
            }
        }
        return $this->render('add-permission',['model'=>$model]);
    }
    //修改权限
    public function actionEditPermission($name){
        $permission=\Yii::$app->authManager->getPermission($name);
        if($permission==null){
            throw new NotFoundHttpException('权限不存在！');
        }
        $model=new PermissionForm();
        //将要修改的权限赋值给表单模型
        $model->loadDate($permission);
        if($model->load(\Yii::$app->request->post()) && $model->validate()){
            if($model->updatePermission($name)){
                \Yii::$app->session->setFlash('权限修改成功！');
                return $this->redirect('permission-index');
            }
        }
        return $this->render('add-permission',['model'=>$model]);
    }
    //删除权限
    public function actionDelPermission($name){
        $permission=\Yii::$app->authManager->getPermission($name);
        if($permission==null){
            throw new NotFoundHttpException('权限不存在！');
        }
        \Yii::$app->authManager->remove($permission);
        \Yii::$app->session->setFlash('删除权限成功');
        return $this->redirect('permission-index');
    }


    //角色列表
    public function actionRoleIndex(){
        $models=\Yii::$app->authManager->getRoles();
        return $this->render('role-index',['models'=>$models]);
    }
    //添加角色
    public function actionAddRole(){
        $model=new RoleForm();
        if($model->load(\Yii::$app->request->post()) && $model->validate()){
            if($model->addRole()){
                \Yii::$app->session->setFlash('角色添加成功！');
                return $this->redirect('role-index');
            }
        }
        return $this->render('add-role',['model'=>$model]);
    }
    //修改角色
    public function actionEditRole($name){
        $role=\Yii::$app->authManager->getRole($name);
        if($role==null){
            throw new NotFoundHttpException('角色不存在！');
        }
        $model=new RoleForm();
        //已有的权限默认选中 当$model->permission中有值的时候便自动选择
        $permission=\Yii::$app->authManager->getPermissionsByRole($name);
        foreach ($permission as $permissionName){
            $model->permissions[]=$permissionName->name;
        }

        $model->loadDate($role);
        if($model->load(\Yii::$app->request->post()) && $model->validate()){
            if($model->updateRole($name)){
                \Yii::$app->session->setFlash('修改角色成功');
                return $this->redirect('role-index');
            }
        }
        return $this->render('add-role',['model'=>$model]);
    }

    //删除角色
    public function actionDelRole($name){
        $role=\Yii::$app->authManager->getRole($name);
        if($role==null){
            throw new NotFoundHttpException('角色不存在！');
        }
        \Yii::$app->authManager->remove($role);
        \Yii::$app->session->setFlash('删除角色成功');
        return $this->redirect('role-index');
    }
}
