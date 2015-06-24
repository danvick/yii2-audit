<?php

namespace bedezign\yii2\audit\panels;

use Yii;

class AssetPanel extends \yii\debug\panels\AssetPanel
{
    public function getDetail()
    {
        return Yii::$app->view->render('@yii/debug/views/default/panels/assets/detail', [
            'panel' => $this,
        ]);
    }
}