<?php

namespace common\models\elasticsearch;

use common\components\elasticsearch\ActiveQuery;

class CompanyQuery extends ActiveQuery
{
    public static function name($str, $type = 'match')
    {
        return [$type => ['name' => $str]];
    }

    public static function address($str)
    {
        return ['match' => ['address' => $str]];
    }

    public static function businessScope($str)
    {
        return ['match' => ['business_scope' => $str]];
    }

    public static function legalPerson($str)
    {
        return ['match' => ['legal_person' => $str]];
    }
}