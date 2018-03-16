<?php
namespace frontend\controllers;

use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;
use common\models\ChinesePinyin;
use frontend\models\UploadForm;
use yii\web\UploadedFile;
use \Yunpian\Sdk\YunpianClient;

/**
 * Site controller
 */
class TestController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
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
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $string = '徐志坚';

        $stringList = array();

        $len = mb_strlen($string);
        while ($len) {
            $stringList[] = mb_substr($string, 0, 1, 'utf8');
            $string = mb_substr($string, 1, $len, 'utf8');
            $len = mb_strlen($string);
        }

        var_dump($stringList);


        $pinyinList = array();


        if (!is_array($stringList)) {
            return $pinyinList;
        }

        foreach ($stringList as $string) {
            if ((strlen($string) === 3) && isset(ChinesePinyin::$chinesePinyin[$string])) {
            // 大部分汉字strlen长度为3，在拼音库里。所有读音都取出。
                $pinyinList[] = ChinesePinyin::$chinesePinyin[$string];
            } else {
                $pinyinList[] = array($string);
            }
        }

        var_dump($pinyinList);

        $logogram = '';
        foreach ($pinyinList as $key => $value) {
            $logogram .= substr($value[0], 0, 1);
            var_dump(substr($value[0], 0, 1));
        }

        var_dump($logogram);
    }

    public function actionUpload($value='')
    {
        $model = new UploadForm();

        if (Yii::$app->request->isPost) {
            $model->imageFiles = UploadedFile::getInstances($model, 'imageFiles');
            if ($model->upload()) {
                // 文件上传成功
                return;
            }
        }

        return $this->render('upload', ['model' => $model]);
    }

    public function actionLockDb($value='')
    {
        // \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        // $headers = Yii::$app->response->headers;
        // var_dump($headers);

        // $db = Yii::$app->db;

        /*$rs = $db->createCommand()->batchInsert('dk_order', ['user_id', 'order_no'], [
            [1, uniqid('od_',true)],
            [2, uniqid('od_',true)],
            [3, uniqid('od_',true)],
        ])->execute();*/

        $command = Yii::$app->db->createCommand('SELECT * FROM dk_order');
        $all = $command->queryAll();
        var_dump($all);
    }

    public function actionSendMsg($value='')
    {
        $apikey = '5604c7311950aea5a1d83c0e87c41b45';

        //初始化client,apikey作为所有请求的默认值
        $clnt = YunpianClient::create($apikey);

        $param = [YunpianClient::MOBILE => '15652929681',YunpianClient::TEXT => '【秉象科技】您的验证码是1234'];
        $r = $clnt->sms()->single_send($param);
        var_dump($r);
        if($r->isSucc()){
            $rs = $r->data();
            var_dump($rs);
        }

        //账户$clnt->user() 签名$clnt->sign() 模版$clnt->tpl() 短信$clnt->sms() 语音$clnt->voice() 流量$clnt->flow() 视频短信$clnt->vsms()
    }
}
