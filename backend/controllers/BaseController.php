<?php

namespace backend\controllers;

use Yii;
use yii\helpers\Json;
use yii\web\Controller;

/**
 * base module下controller基类
 *
 */
class BaseController extends Controller
{
    protected $except = ['login','error'];

    public function init()
    {
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        // Access judgement
        if (Yii::$app->session->get(Yii::$app->session->name) === null && !in_array($action->id, $this->except)) {
            return !Yii::$app->response->redirect(['site/login']);
        }

        return true;
    }
}
