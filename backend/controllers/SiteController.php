<?php
namespace backend\controllers;

use Yii;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use backend\models\Admin;

/**
 * Site controller
 */
class SiteController extends BaseController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        var_dump(Yii::$app->session->get(Yii::$app->session->name));
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {
        try {
            if (!Yii::$app->session->get(Yii::$app->session->name) === null) {
                return $this->goHome();
            }
            
            $model = new Admin();
            if (Yii::$app->request->isPost) {
                $params = Yii::$app->request->post();
                if ($model->load($params) && $model->login()) {
                    return $this->render('index');
                }
            }
        } catch (Exception $e) {
            var_dump($e);
        }
        
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Register
     */
    public function actionRegister()
    {
        $params = Yii::$app->request->post();

        try {
            $model = new Admin();
            $model->load($params);
            $model->role_id = $params['role_id'] ?? 0;
            $model->register();

            return $this->redirect(['site/index']);
        } catch (\Exception $e) {
            var_dump($e);
            Yii::info($e);
        }
        
        return $this->render('error',['name'=>'Register','message'=>'注册失败']);
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->session->destroy();

        return $this->goHome();
    }
}
