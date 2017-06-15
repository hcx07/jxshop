<?php

namespace backend\controllers;

use backend\models\ArticleCategory;
use yii\data\Pagination;
use yii\filters\AccessControl;

class ArticleCategoryController extends \yii\web\Controller
{
    public function actionIndex()
    {
        $query=ArticleCategory::find();
        $total=$query->count();
        $page=new Pagination([
            'totalCount'=>$total,
            'defaultPageSize'=>3,
        ]);
        $model=$query->offset($page->offset)->limit($page->limit)->orderBy(['id'=>SORT_DESC])->all();
//        $model=ArticleCategory::find()->all();
        return $this->render('index',['model'=>$model,'page'=>$page]);
    }
    public function actionAdd(){
        $model=new ArticleCategory();
        $model->status=0;
        $model->is_help=0;
        if($model->load(\Yii::$app->request->post())){
            if($model->validate()){
                $model->save();
                \Yii::$app->session->setFlash('success','添加成功！');
                return $this->redirect(['article-category/index']);
            }else{
                var_dump($model->getFirstErrors());exit;
            }
        }
        return $this->render('add',['model'=>$model]);
    }
    public function actionEdit($id){
        $model=ArticleCategory::findOne(['id'=>$id]);
        if($model->load(\Yii::$app->request->post())){
            if($model->validate()){
                $model->save();
                \Yii::$app->session->setFlash('success','修改成功！');
                return $this->redirect(['article-category/index']);
            }else{
                var_dump($model->getFirstErrors());exit;
            }
        }
        return $this->render('add',['model'=>$model]);
    }
    public function actionDel($id){
        $model=ArticleCategory::findOne(['id'=>$id]);
        $model->status=-1;
        $model->save();
//        var_dump($model);exit;
        return $this->redirect(['article-category/index']);
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
