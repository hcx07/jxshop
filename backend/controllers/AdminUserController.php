<?php

namespace backend\controllers;

use backend\models\AdminUser;
use backend\models\SignupForm;
use yii\filters\AccessControl;

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
    //权限管理，只有登陆了才能操作
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => [],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

}
