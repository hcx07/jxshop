<?php

namespace backend\controllers;



use backend\models\Menu;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;
use yii\web\Controller;

class MenuController extends Controller
{
    public function actionIndex()
    {
        $query = Menu::find();
        $total = $query->count();
        $page = new Pagination([
            'totalCount' => $total,
            'defaultPageSize' => 10,
        ]);
        $model = $query->offset($page->offset)->limit($page->limit)->orderBy(['id' => SORT_DESC])->all();
        return $this->render('index', ['model' => $model, 'page' => $page]);
    }

    public function actionAdd(){
        $model=new Menu();
        $parent=['0'=>'顶级菜单']+ArrayHelper::map(Menu::findAll(['parent_id'=>0]),'id','label');
        if($model->load(\Yii::$app->request->post())){
            if($model->validate()){
                $model->save();
                \Yii::$app->session->setFlash('success','菜单添加成功！');
                return $this->redirect(['menu/index']);
            }else{
                var_dump($model->getFirstErrors());exit;
            }
        }
        return $this->render('add',['model'=>$model,'parent'=>$parent]);
    }

    public function actionEdit($id){
        $model=Menu::findOne(['id'=>$id]);
        $parent=['0'=>'顶级菜单']+ArrayHelper::map(Menu::findAll(['parent_id'=>0]),'id','label');
        if($model->load(\Yii::$app->request->post())){
            if($model->validate()){
                $model->save();
                \Yii::$app->session->setFlash('success','菜单修改成功！');
                return $this->redirect(['menu/index']);
            }else{
                var_dump($model->getFirstErrors());exit;
            }
        }
        return $this->render('add',['model'=>$model,'parent'=>$parent]);
    }
    public function actionDel($id){
        $model=Menu::findOne(['id'=>$id]);
        $model->save();
//        var_dump($model);exit;
        return $this->redirect(['menu/index']);
    }


}
