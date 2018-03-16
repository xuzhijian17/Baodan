<?php
namespace backend\models;

use Yii;
use yii\db\Query;
use yii\base\ErrorException;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\web\IdentityInterface;

/**
 * Product model
 *
 */
class Product extends BaseModel 
{
    public $page;
    public $pageSize;

    // table name
    public $tableName = 'dk_product';


    public function getList($page=1,$pageSize=10)
    {
        $data = (new Query())
            ->from($this->tableName)
            ->limit($pageSize)
            ->offset(($page-1)*$pageSize)
            ->orderBy(['updated_at'=>SORT_DESC,'created_at'=>SORT_DESC])
            ->all();

        return $data;
    }

    public function add($name='',$department_id)
    {
        $rs = Yii::$app->db->createCommand()->insert($this->table, [
            'name' => $name,
            'department_id' => $department_id,
        ])->execute();

        return $rs;
    }
}
