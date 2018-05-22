<?php

namespace frontend\models;

use common\models\elasticsearch\Company;
use common\models\elasticsearch\CompanyQuery;
use frontend\components\ActiveDataProvider;
use common\models\UserClue;
use frontend\components\Tools;

class UserClueSearch extends UserClue
{
    public function rules()
    {
        return [
            [['company.name'], 'string', 'max' => 255],
        ];
    }

    public function attributes()
    {
        return array_merge(parent::attributes(), ['company.name']);
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'company.name' => '企业名称',
        ]);
    }

    public function search($params)
    {
        $query = UserClue::findInTeam()->andWhere(['user_id' => Tools::getCurrentUserId()]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'updated_at' => SORT_DESC
                ]
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        if ($companyName = $this->getAttribute('company.name')) {
            $ids = Company::findIds([
                'bool' => [
                    'must' => [
                        CompanyQuery::name($companyName, 'match_phrase'),
                        [
                            'ids' => ['values' => $query->select('company_id')->column()]
                        ]
                    ]
                ]
            ]);
            $query->andWhere(['company_id' => $ids]);
        }

        return $dataProvider;
    }
}
