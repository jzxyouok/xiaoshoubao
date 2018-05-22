<?php

namespace common\models;

use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "province".
 *
 * @property integer $id
 * @property string $name
 * @property integer $status
 * @property integer $sort
 * @property integer $created_at
 * @property integer $created_by
 *
 * @property Area[] $areas
 * @property City[] $cities
 */
class Province extends \common\models\base\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'province';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['status', 'sort', 'created_at', 'created_by'], 'integer'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '名称',
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

        $hasCity = City::find()->select(['id'])->where(['province_id' => $this->id])->limit(1)->one();
        if ($hasCity) {
            $this->addError('id', '该省份下面已有城市，不能删除');
            return false;
        }

        return true;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAreas()
    {
        return $this->hasMany(Area::className(), ['province_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCities()
    {
        return $this->hasMany(City::className(), ['province_id' => 'id']);
    }

    /**
     * 得到所有的省份
     * @param bool $map
     * @return array
     */
    public static function findIdNames($map = false)
    {
        $model = self::find()->select(['id', 'name'])->asArray()->all();
        if ($map) {
            return ArrayHelper::map($model, 'id', 'name');
        }
        return $model;
    }

}
