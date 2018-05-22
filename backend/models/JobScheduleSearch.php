<?php

namespace backend\models;

use common\components\ActiveDataProvider;
use common\models\JobSchedule;

class JobScheduleSearch extends JobSchedule
{
    public function rules()
    {
        return [
            [['job_name'], 'string'],
            [['status'], 'integer'],
        ];
    }

    public function search($params)
    {
        $query = JobSchedule::find();

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
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'job_name', $this->job_name]);

        return $dataProvider;
    }
}
