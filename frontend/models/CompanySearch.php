<?php

namespace frontend\models;

use common\components\elasticsearch\ActiveDataProvider;
use common\models\elasticsearch\Company;
use common\models\elasticsearch\CompanyQuery;
use yii\base\Model;

class CompanySearch extends Model
{
    /**
     * 查询内容
     * @var string
     */
    public $s;

    public function rules()
    {
        return [
            [['s'], 'string'],
        ];
    }

    public function search($params)
    {
        $query = Company::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params, '');

        if (!$this->validate()) {
            return $dataProvider;
        }

        $keywords = array_filter(explode(' ', $this->s)) ?: [];
        $queryMath = [];
        foreach ($keywords as $keyword) {
            $queryMath[] = CompanyQuery::name($keyword);
            $queryMath[] = CompanyQuery::address($keyword);
            $queryMath[] = CompanyQuery::businessScope($keyword);
            $queryMath[] = CompanyQuery::legalPerson($keyword);
        }
        $query->query([
            'bool' => [
                'should' => $queryMath
            ]
        ]);
        $query->highlightQuick(['name', 'address', 'business_scope', 'legal_person']);

        return $dataProvider;
    }
}
