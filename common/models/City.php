<?php

namespace common\models;

use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "city".
 *
 * @property integer $id
 * @property integer $province_id
 * @property string $name
 * @property integer $status
 * @property integer $sort
 * @property integer $created_at
 * @property integer $created_by
 *
 * @property Area[] $areas
 * @property Province $province
 */
class City extends \common\models\base\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'city';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['province_id', 'name'], 'required'],
            [['province_id', 'status', 'sort', 'created_at', 'created_by'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['province_id'], 'exist', 'skipOnError' => true, 'targetClass' => Province::className(), 'targetAttribute' => ['province_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'province_id' => '省编号',
            'name' => '城市名',
            'status' => '状态',
            'sort' => '排序',
            'created_at' => '创建时间',
            'created_by' => '创建人',
        ];
    }

    public function beforeDelete()
    {
        if (!parent::beforeDelete()) {
            return false;
        }

        $hasArea = Area::find()->select(['id'])->where(['city_id' => $this->id])->limit(1)->one();
        if ($hasArea) {
            $this->addError('id', '该城市下面已有地区，不能删除');
            return false;
        }

        return true;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAreas()
    {
        return $this->hasMany(Area::className(), ['city_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProvince()
    {
        return $this->hasOne(Province::className(), ['id' => 'province_id']);
    }

    /**
     * 得到在该省份下的城市 id 和 name
     * @param $provinceId
     * @param bool $map
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function findIdNames($provinceId, $map = false)
    {
        $model = self::find()->select(['id', 'name'])->where(['province_id' => $provinceId])->asArray()->all();
        if ($map) {
            return ArrayHelper::map($model, 'id', 'name');
        }
        return $model;
    }

}
