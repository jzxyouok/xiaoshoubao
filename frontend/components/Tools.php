<?php

namespace frontend\components;

use common\models\User;
use Yii;
use yii\helpers\Html;
use yii\web\ForbiddenHttpException;

class Tools extends \common\components\Tools
{
    /**
     * @param bool $throwExceptionIfNoLogin
     * @return User|null
     * @throws ForbiddenHttpException
     */
    public static function getCurrentUser($throwExceptionIfNoLogin = true)
    {
        /** @var User $user */
        $user = Yii::$app->user->identity;
        if (!$user && $throwExceptionIfNoLogin) {
            throw new ForbiddenHttpException('用户未登录');
        }
        return $user;
    }

    /**
     * @param bool $throwExceptionIfNoLogin
     * @return int|string
     * @throws ForbiddenHttpException
     */
    public static function getCurrentUserId($throwExceptionIfNoLogin = true)
    {
        if (Yii::$app->user->isGuest && $throwExceptionIfNoLogin) {
            throw new ForbiddenHttpException('用户未登录');
        }
        return Yii::$app->user->id;
    }

    /**
     * @param bool $throwExceptionIfNoLogin
     * @return int
     */
    public static function getCurrentUserTeamId($throwExceptionIfNoLogin = true)
    {
        $user = static::getCurrentUser($throwExceptionIfNoLogin);
        return $user->team_id;
    }

    /**
     * url 补全 schema
     * @param $url
     * @param string $schema
     * @return string
     */
    public static function urlAddSchema($url, $schema = 'http')
    {
        $originSchema = substr($url, 0, 7);
        if ($originSchema == 'http://' || $originSchema == 'https:/') {
            return $url;
        }
        $url = $schema . '://' . $url;
        return $url;
    }

    /**
     * 高亮关键字
     * @param $str
     * @param array|string $keywords
     * @return mixed
     */
    public static function highLightKeyWords($str, $keywords)
    {
        if (!is_array($keywords)) {
            $keywords = [$keywords];
        }
        foreach ($keywords as $keyword) {
            $str = str_replace($keyword, Html::tag('strong', $keyword, ['class' => 'text-red']), $str);
        }
        return $str;
    }

    /**
     * 总数大约的显示方式
     * @param $totalCount
     * @return mixed
     */
    public static function totalCountAbout($totalCount)
    {
        if ($totalCount < 100) {
            return $totalCount;
        } elseif ($totalCount < 1000) {
            return intval($totalCount / 100) * 100 . '+';
        } elseif ($totalCount < 10000) {
            return intval($totalCount / 1000) * 1000 . '+';
        } else {
            return '10000+';
        }
    }
}