<?php

namespace frontend\widgets;

use kriss\widgets\LinkPagerWithSubmit;
use yii\widgets\ListView;
use yii\widgets\Pjax;

class SimpleListView extends ListView
{
    /**
     * 是否可以跳页
     * @var bool
     */
    public $canJump = true;
    /**
     * 是否允许 pjax
     * @var bool
     */
    public $enablePjax = false;

    public $layout = "<div class='box-header'>{summary}</div>\n{items}\n<div class='text-center'>{pager}</div>";

    public function init()
    {
        parent::init();
        if (!$this->pager) {
            $this->pager = [
                'firstPageLabel' => '第一页',
                'lastPageLabel' => '最后一页',
            ];
            if ($this->canJump) {
                $this->pager = array_merge($this->pager, [
                    'class' => LinkPagerWithSubmit::className(),
                    'minPageSize' => 1,
                    'pageSizeLabel' => '每页',
                    'pageLabel' => '当前',
                    'submitButtonLabel' => '确定',
                ]);
            }
        }
    }

    public function run()
    {
        if ($this->enablePjax) {
            Pjax::begin();
            parent::run();
            Pjax::end();
        } else {
            parent::run();
        }
    }
}