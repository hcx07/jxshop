<?php

namespace backend\controllers;

use backend\components\Helper;
use backend\models\AdminUser;
use backend\models\RoleForm;
use backend\models\SignupForm;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

class AdminUserController extends \yii\web\Controller
{
    public function actionIndex()
    {
        $model=AdminUser::find()->all();
        return $this->render('index',['model'=>$model]);
    }
    public function actionAdd()
    {
        $model = new AdminUser();
//        var_dump(\Yii::$app->request->post());exit;
        if ($model->load(\Yii::$app->request->post())) {
            if ($model->validate()) {
                $hash=\Yii::$app->security->generatePasswordHash($model->password);
//                    Yii::$app->security->generatePasswordHash('123456');
                $model->password_hash=$hash;
                $model->last_login=time();
                $model->last_ip=\Yii::$app->request->getUserIP();
                $model->save();
                \Yii::$app->session->setFlash('success', '添加成功！');
                return $this->redirect(['admin-user/index']);
            }
        }
        return $this->render('add', ['model' => $model]);
    }
    public function actionEdit($id)
    {
        $model = AdminUser::findOne(['id'=>$id]);
//        var_dump(\Yii::$app->request->post());exit;
        if ($model->load(\Yii::$app->request->post())) {
            if ($model->validate()) {
                $hash=\Yii::$app->security->generatePasswordHash($model->password);
//                    Yii::$app->security->generatePasswordHash('123456');
                $model->password_hash=$hash;
                $model->last_login=time();
                $model->last_ip=\Yii::$app->request->getUserIP();
                $model->save();
                \Yii::$app->session->setFlash('success', '添加成功！');
                return $this->redirect(['admin-user/index']);
            }
        }
        return $this->render('add', ['model' => $model]);
    }
    public function actionDel($id){
        $model = AdminUser::findOne(['id'=>$id]);
        $model->delete();
        return $this->redirect(['admin-user/index']);
    }

    //将角色和用户关联
    public function actionRole($id){
        $model = AdminUser::findOne(['id'=>$id]);
        $username=$model->username;
        //已有的角色默认选中  当$model->role中有role值的时候便自动选中
        $roles =\Yii::$app->authManager->getRolesByUser($id);
        foreach ($roles as $role){
            $model->role[] = $role->name;
        }
        if ($model->load(\Yii::$app->request->post())) {
            if($model->updateRole($model,$id)){
                \Yii::$app->session->setFlash('修改权限成功');
                return $this->redirect('index');
            }
        }
        return $this->render('role', ['model' => $model,'username'=>$username]);
    }
}
