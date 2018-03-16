<?php
namespace backend\models;

use Yii;
use yii\db\Query;
use yii\base\ErrorException;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\web\IdentityInterface;

/**
 * Admin model
 *
 */
class Admin extends BaseModel 
{
    public $username;
    public $password;
    public $role_id;

    // table name
    public $tableName = 'dk_admin';

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * Returns a list of scenarios and the corresponding active attributes.
     * An active attribute is one that is subject to validation in the current scenario.
     * @return array a list of scenarios and the corresponding active attributes.
     */
    public function scenarios($value='')
    {
        return [
            'default' => ['username','password'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password'], 'required'],
        ];
    }


    /**
     * Logs in a user using the provided username and password.
     *
     * @return bool whether the user is logged in successfully
     */
    public function login()
    {
        /*if (!Yii::$app->session->get(Yii::$app->session->name) === null) {
            return true;
        }*/

        $userData = (new Query)
            ->select(['username','password'])
            ->from($this->tableName)
            ->where(['status'=>0])
            ->orWhere(['username'=>$this->username])
            ->one()
        ;
        
        if (empty($userData)) {
            return false;
            // throw new \Exception(Yii::$app->params['codeinfo']['501'], 501);
        }

        if (!Yii::$app->security->validatePassword($this->password, $userData['password'])) {
            return false;
            // throw new \Exception(Yii::$app->params['codeinfo']['502'], 502);
        }
        
        Yii::$app->session->set(Yii::$app->session->name,$userData['username']);
        
        return true;
    }


    /**
     * Register in a user using the provided username and password.
     *
     * @return bool whether the user is registered in successfully
     */
    public function register()
    {
        $userRow = (new Query)
            ->from($this->tableName)
            ->where(['username'=>$this->username,'status'=>0])
            ->count()
        ;

        if (!empty($userRow)) {
            throw new \Exception(Yii::$app->params['codeinfo'][503], 503);
        }
        
        $rs = Yii::$app->db->createCommand()->insert($this->tableName, [
            'username' => $this->username,
            'password' => Yii::$app->security->generatePasswordHash($this->password),
            'role_id' => $this->role_id,
            'created_at' => date('Y-m-d H:i:s')
        ])->execute();

        return $rs;
    }

    public function getList($page=1,$pageSize=10)
    {
        $data = (new Query())
            ->from($this->tableName)
            ->where(['status'=>0])
            ->limit($pageSize)
            ->offset(($page-1)*$pageSize)
            ->orderBy(['updated_at'=>SORT_DESC,'created_at'=>SORT_DESC])
            ->all();

        return $data;
    }

    public function add($username,$password,$role_id)
    {
        $userRow = (new Query)
            ->from($this->tableName)
            ->where(['username'=>$username])
            ->count()
        ;

        if (!empty($userRow)) {
            throw new \Exception(Yii::$app->params['codeinfo'][503], 503);
        }

        $rs = Yii::$app->db->createCommand()->insert($this->table, [
            'username' => $username,
            'password' => Yii::$app->security->generatePasswordHash($password),
            'role_id' => $role_id,
        ])->execute();

        return $rs;
    }
}
