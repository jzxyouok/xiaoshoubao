<?php
/**
 * DetailView的简单封装
 * 改成用 dl dt dd
 */

namespace common\widgets;

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\DetailView;

class DLHorizontal extends DetailView
{
    public $template = '<p><dt{captionOptions}>{label}</dt><dd{contentOptions}>{value}</dd></p>';

    public $options = ['class' => 'dl-horizontal col-sm-12'];

    public function run()
    {
        $rows = [];
        $i = 0;
        foreach ($this->attributes as $attribute) {
            $attribute['value'] = $attribute['value'] ?: '-';
            $rows[] = $this->renderAttribute($attribute, $i++);
        }

        $options = $this->options;
        $tag = ArrayHelper::remove($options, 'tag', 'dl');
        echo Html::tag($tag, implode("\n", $rows), $options);
    }
}