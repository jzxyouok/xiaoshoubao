<?php

namespace frontend\models\form;

use common\models\Record;
use common\models\UserClue;
use frontend\components\Tools;
use yii\base\Exception;
use yii\base\Model;
use yii\web\NotFoundHttpException;

class UserClueRecordForm extends Model
{
    /**
     * @var UserClue
     */
    public $userClue;

    public $type;

    public $content;

    public function rules()
    {
        return [
            [['type', 'content'], 'required'],
            ['type', 'integer'],
            ['content', 'string', 'max' => 200],
        ];
    }

    public function attributeLabels()
    {
        return [
            'type' => '类型',
            'content' => '内容'
        ];
    }

    public function init()
    {
        if (!$this->userClue instanceof UserClue) {
            throw new NotFoundHttpException('userClue 必须是 UserClue 的实例');
        }
    }

    /**
     * @return bool
     */
    public function create()
    {
        try {
            Record::createUserOperation(Tools::getCurrentUserTeamId(), $this->userClue->company_id, $this->type, $this->content);
        } catch (Exception $exception) {
            $this->addError('type', $exception->getMessage());
            return false;
        }
        return true;
    }
}