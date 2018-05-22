<?php

namespace backend\models;

use common\components\ActiveDataProvider;
use common\models\Area;

class AreaSearch extends Area
{
    public function rules()
    {
        return [
            [['name'], 'string'],
            [['province_id', 'city'], 'integer']
        ];
    }

    public function search($params)
    {
        $query = Area::find()->with(['province', 'city']);

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

        $query->andFilterWhere([
            'province_id' => $this->province_id,
            'city_id' => $this->city_id
        ]);
        $query->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}
