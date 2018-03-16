<?php
namespace backend\models;

use Yii;
use yii\db\Query;
use yii\base\ErrorException;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\web\IdentityInterface;

/**
 * User model
 *
 */
class User extends BaseModel 
{
    public $username;
    public $password;
    public $department_id;

    // table name
    public $tableName = 'dk_user';


    public function getUserList($value='')
    {
        $data = (new Query())
            ->from($this->tableName)
            ->limit($this->pageSize)
            ->offset(($this->page-1)*$this->pageSize)
            ->orderBy(['updated_at'=>SORT_DESC,'created_at'=>SORT_DESC])
            ->all();

        return $data;
    }
}
