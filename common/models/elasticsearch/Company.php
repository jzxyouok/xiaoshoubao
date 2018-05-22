<?php

namespace common\models\elasticsearch;

use common\components\elasticsearch\DbBasedActiveRecord;

/**
 * This is the model class for table "company".
 *
 * @property integer $id
 * @property string $name
 * @property string $registration_number
 * @property string $type_code
 * @property string $type_name
 * @property string $type_business
 * @property string $social_credit_code
 * @property string $organization_code
 * @property string $legal_person
 * @property string $establishment_date
 * @property string $registered_capital
 * @property string $business_scope
 * @property string $shareholder_information
 * @property string $leading_member
 * @property string $cellphone
 * @property string $mail
 * @property string $address
 * @property string $website
 * @property string $branch
 * @property string $business_status
 * @property string $history_name
 * @property string $province
 * @property string $business_term
 * @property string $issue_date
 * @property string $registration_authority
 * @property string $change_record
 * @property integer $status
 * @property integer $created_at
 * @property integer $created_by
 * @property integer $updated_at
 * @property integer $updated_by
 */
class Company extends DbBasedActiveRecord
{
    const DB_MODEL_CLASS = 'common\models\CompanyUpload';

    /**
     * @inheritdoc
     */
    protected static function mapping()
    {
        $keyword = ['type' => 'keyword'];
        $text = ['type' => 'text'];
        $textIk = [
            'type' => 'text',
            'analyzer' => 'ik_max_word',
            'search_analyzer' => 'ik_max_word'
        ];
        $date = [
            'type' => 'date',
            'format' => 'yyyy-MM-dd||yyyy年MM月dd日'
        ];
        $halfFloat = ['type' => 'half_float'];
        return [
            static::type() => [
                'properties' => [
                    'name' => $textIk,
                    //'registration_number' => $text,
                    //'type_code' => $keyword,
                    //'type_name' => $text,
                    //'type_business' => $keyword,
                    //'social_credit_code' => $text,
                    //'organization_code' => $text,
                    'legal_person' => $textIk,
                    'establishment_date' => $date,
                    'registered_capital' => $halfFloat,
                    'business_scope' => $textIk,
                    //'shareholder_information' => $textIk,
                    //'leading_member' => $textIk,
                    //'cellphone' => $text,
                    //'mail' => $text,
                    'address' => $textIk,
                    //'website' => $text,
                    //'branch' => $textIk,
                    //'business_status' => $keyword,
                    //'history_name' => $textIk,
                    //'province' => $textIk,
                    //'business_term' => $date,
                    //'issue_date' => $date,
                    //'registration_authority' => $textIk,
                    //'change_record' => $textIk,
                ]
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function getSyncDataAttributes($model)
    {
        $attributes = $model->attributes;
        $attributes['registered_capital'] = floatval($attributes['registered_capital']);
        return $attributes;
    }

    /**
     * 获取所有 id
     * @param $query
     * @param int $maxSize
     * @return array
     */
    public static function findIds($query, $maxSize = 1000)
    {
        return static::find()->source([
            'includes' => ['id']
        ])->query($query)->limit($maxSize)->column('id');
    }
}