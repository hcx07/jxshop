<?php

namespace backend\controllers;
use backend\models\GoodsCategory;
use yii\data\Pagination;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

class GoodsCategoryController extends \yii\web\Controller
{
    public function actionIndex()
    {
        $query=GoodsCategory::find();
        $total=$query->count();
        $page=new Pagination([
            'totalCount'=>$total,
            'defaultPageSize'=>5,
        ]);
        $model=$query->offset($page->offset)->limit($page->limit)->orderBy(['tree'=>SORT_ASC,'lft'=>SORT_ASC])->all();
//        $model=ArticleCategory::find()->all();
        return $this->render('index',['model'=>$model,'page'=>$page]);
    }

    public function actionTest(){
        $countries = new GoodsCategory();
        $countries->name='aaa';
        $countries->parent_id=0;
        $countries->makeRoot();
    }
    public function actionAdd(){
        $model=new GoodsCategory();
        if($model->load(\Yii::$app->request->post()) && $model->validate()){
            if($model->parent_id){
                //添加非一级分类
                $parent = GoodsCategory::findOne(['id'=>$model->parent_id]);//获取上一级分类
                $model->prependTo($parent);//添加到上一级分类下面
            }else{
                $model->makeRoot();
            }
            \Yii::$app->session->setFlash('success','添加成功');
            return $this->redirect(['goods-category/index']);
        }
        $categories = ArrayHelper::merge([['id'=>0,'name'=>'顶级分类','parent_id'=>0]],GoodsCategory::find()->asArray()->all());
        return $this->render('add',['model'=>$model,'categories'=>$categories]);
    }
    public function actionEdit($id){
        $model=GoodsCategory::findOne(['id'=>$id]);
        if($model==null){
            throw new NotFoundHttpException('分类不存在');
        }
        if($model->load(\Yii::$app->request->post()) && $model->validate()){
            if($model->parent_id){
                //添加非一级分类
                $parent = GoodsCategory::findOne(['id'=>$model->parent_id]);//获取上一级分类
                $model->prependTo($parent);//添加到上一级分类下面
            }else{
                if($model->getOldAttribute('parent_id')==0){
                    $model->save();
                }else{
                    $model->makeRoot();
                }
            }
            \Yii::$app->session->setFlash('success','修改成功');
            return $this->redirect(['goods-category/index']);
        }
        $categories = ArrayHelper::merge([['id'=>0,'name'=>'顶级分类','parent_id'=>0]],GoodsCategory::find()->asArray()->all());
        return $this->render('add',['model'=>$model,'categories'=>$categories]);
    }

}

