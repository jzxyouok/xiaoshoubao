<?php

namespace backend\models;

use common\components\ActiveDataProvider;
use common\models\City;

class CitySearch extends City
{
    public function rules()
    {
        return [
            [['name'], 'string'],
            [['province_id'],'integer']
        ];
    }

    public function search($params)
    {
        $query = City::find()->with(['province']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_DESC,
                    'id' => SORT_DESC,
                ]
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}
