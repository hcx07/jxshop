<?php

namespace backend\controllers;

use backend\models\Article;
use backend\models\ArticleDetail;
use yii\data\Pagination;
use yii\filters\AccessControl;
use yii\web\ForbiddenHttpException;

class ArticleController extends \yii\web\Controller
{
    public function actionIndex()
    {
        $query=Article::find();
//        $detail=ArticleDetail::find();
        $total=$query->count();
        $page=new Pagination([
            'totalCount'=>$total,
            'defaultPageSize'=>3,
        ]);
        $model=$query->offset($page->offset)->limit($page->limit)->orderBy(['id'=>SORT_DESC])->all();
//        $model=Article::find()->all();
        return $this->render('index',['model'=>$model,'page'=>$page]);
    }
    public function actionAdd(){
        if (!\Yii::$app->user->can('article_add')) {
            throw new ForbiddenHttpException('对不起，权限不足');
        }
        $model=new Article();
        $detail=new ArticleDetail();
        $model->status=0;
        if($model->load(\Yii::$app->request->post())&&$detail->load(\Yii::$app->request->post())){
            if($model->validate()&&$detail->validate()){
                $model->create_time=time();
                $model->save();
                $detail->article_id=$model->id;
                $detail->save();
                \Yii::$app->session->setFlash('success','添加成功！');
                return $this->redirect(['article/index']);
            }else{
                var_dump($model->getFirstErrors());
                var_dump($detail->getFirstErrors());
                exit;
            }
        }
        return $this->render('add',['model'=>$model,'detail'=>$detail]);
    }
    public function actionEdit($id){
        if (!\Yii::$app->user->can('article_Eedit')) {
            throw new ForbiddenHttpException('对不起，权限不足');
        }
        $model=Article::findOne(['id'=>$id]);
        $detail=ArticleDetail::findOne(['article_id'=>$id]);
        if($model->load(\Yii::$app->request->post())&&$detail->load(\Yii::$app->request->post())){
            if($model->validate()&&$detail->validate()){
                $model->create_time=time();
                $model->save();
                $detail->article_id=$model->id;
                $detail->save();
                \Yii::$app->session->setFlash('success','修改成功！');
                return $this->redirect(['article/index']);
            }else{
                var_dump($model->getFirstErrors());
                var_dump($detail->getFirstErrors());
                exit;
            }
        }
        return $this->render('add',['model'=>$model,'detail'=>$detail]);
    }
    public function actionDel($id){
        if (!\Yii::$app->user->can('article_del')) {
            throw new ForbiddenHttpException('对不起，权限不足');
        }
        $model=Article::findOne(['id'=>$id]);
        $model->status=-1;
        $model->save();
//        var_dump($model);exit;
        return $this->redirect(['article/index']);
    }
}
