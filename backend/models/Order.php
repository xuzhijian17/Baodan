<?php
namespace backend\models;

use Yii;
use yii\db\Query;
use yii\base\ErrorException;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\web\IdentityInterface;

/**
 * Order model
 *
 */
class Order extends BaseModel 
{
    public $page;
    public $pageSize;

    // table name
    public $tableName = 'dk_order';

    /**
     * Returns a list of scenarios and the corresponding active attributes.
     * An active attribute is one that is subject to validation in the current scenario.
     * @return array a list of scenarios and the corresponding active attributes.
     */
    public function scenarios($value='')
    {
        return [
            'default' => ['page','pageSize'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['page','default','value'=>1],
            ['pageSize','default','value'=>10]
        ];
    }

    public function getOrderList($value='')
    {
        $orderData = (new Query())
            ->from($this->tableName)
            ->limit($this->pageSize)
            ->offset(($this->page-1)*$this->pageSize)
            ->orderBy(['updated_at'=>SORT_DESC,'created_at'=>SORT_DESC])
            ->all();

        return $orderData;
    }

    public function getOverOrderList($uid='')
    {
        $orderData = (new Query())
            ->from($this->tableName)
            ->where(['status'=>2])
            ->limit($this->pageSize)
            ->offset(($this->page-1)*$this->pageSize)
            ->orderBy(['updated_at'=>SORT_DESC,'created_at'=>SORT_DESC])
            ->all();

        return $orderData;
    }

    public function settleAccount($id='')
    {
        $rs = Yii::$app->db->createCommand()->update($this->table, [
            'status' => 3,
            'updated_at' => date('Y-m-d H:i:s')
        ],['id' => $id, 'status'=>2])->execute();

        return $rs;
    }
}
