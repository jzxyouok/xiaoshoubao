<?php

namespace backend\models;

use common\components\ActiveDataProvider;
use common\models\CompanyUpload;

class CompanyUploadSearch extends CompanyUpload
{
    public function rules()
    {
        return [
            [['name'], 'string'],
        ];
    }

    public function search($params)
    {
        $query = CompanyUpload::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'enableCountCache' => true,
            'countCacheTime' => 10
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}
