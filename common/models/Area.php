<?php

namespace common\models;

use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "area".
 *
 * @property integer $id
 * @property integer $province_id
 * @property integer $city_id
 * @property string $name
 * @property integer $status
 * @property integer $sort
 * @property integer $created_at
 * @property integer $created_by
 *
 * @property City $city
 * @property Province $province
 */
class Area extends \common\models\base\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'area';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['province_id', 'city_id', 'name'], 'required'],
            [['province_id', 'city_id', 'status', 'sort', 'created_at', 'created_by'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['city_id'], 'exist', 'skipOnError' => true, 'targetClass' => City::className(), 'targetAttribute' => ['city_id' => 'id']],
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
            'city_id' => '市编号',
            'name' => '地区名称',
            'status' => '状态',
            'sort' => '排序',
            'created_at' => '创建时间',
            'created_by' => '创建人',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCity()
    {
        return $this->hasOne(City::className(), ['id' => 'city_id']);
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
     * @param $cityId
     * @param bool $map
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function findIdNames($cityId, $map = false)
    {
        $model = self::find()->select(['id', 'name'])->where(['city_id' => $cityId])->asArray()->all();
        if ($map) {
            return ArrayHelper::map($model, 'id', 'name');
        }
        return $model;
    }
}
