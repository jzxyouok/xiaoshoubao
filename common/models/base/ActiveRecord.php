<?php

namespace common\models\base;

use common\models\User;
use Yii;
use yii\base\NotSupportedException;
use yii\db\ActiveQuery;

class ActiveRecord extends \kriss\components\ActiveRecord
{
    /**
     * 在团队范围内查找
     * @param string $teamAttribute
     * @return ActiveQuery
     * @throws NotSupportedException
     */
    public static function findInTeam($teamAttribute = 'team_id')
    {
        if (Yii::$app->user->isGuest) {
            throw new NotSupportedException('登录后才可使用');
        }
        $user = Yii::$app->user->identity;
        if (!$user instanceof User) {
            throw new NotSupportedException('登录用户为 User 对象才能使用');
        }
        return parent::find()->andWhere([$teamAttribute => $user->team_id]);
    }

    /**
     * 关联团队
     * @param string $teamAttribute
     * @throws NotSupportedException
     */
    public function linkTeam($teamAttribute = 'team_id')
    {
        if (Yii::$app->user->isGuest) {
            throw new NotSupportedException('登录后才可使用');
        }
        $user = Yii::$app->user->identity;
        if (!$user instanceof User) {
            throw new NotSupportedException('登录用户为 User 对象才能使用');
        }
        $this->$teamAttribute = $user->team_id;
    }
}