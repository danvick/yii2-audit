<?php

namespace bedezign\yii2\audit\panels;

use bedezign\yii2\audit\components\panels\Panel;
use bedezign\yii2\audit\components\panels\RendersSummaryChartTrait;
use bedezign\yii2\audit\models\AuditTrail;
use bedezign\yii2\audit\models\AuditTrailSearch;
use Yii;
use yii\grid\GridViewAsset;

/**
 * TrailPanel
 * @package bedezign\yii2\audit\panels
 */
class TrailPanel extends Panel
{
    use RendersSummaryChartTrait;

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return \Yii::t('audit', 'Trails');
    }

    /**
     * @inheritdoc
     */
    public function hasEntryData($entry)
    {
        return count($entry->trails) > 0;
    }

    /**
     * @inheritdoc
     */
    public function getLabel()
    {
        return $this->getName() . ' <small>(' . count($this->_model->trails) . ')</small>';
    }

    /**
     * @inheritdoc
     */
    public function getDetail()
    {
        $searchModel = new AuditTrailSearch();
        $params = \Yii::$app->request->getQueryParams();
        $params['AuditTrailSearch']['entry_id'] = $params['id'];
        $dataProvider = $searchModel->search($params);
        $dataProvider->pagination = [
            'pageSize' => 1000,
        ];

        return \Yii::$app->view->render('panels/trail/detail', [
            'panel' => $this,
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getIndexUrl()
    {
        return ['trail/index'];
    }

    /**
     * @inheritdoc
     */
    protected function getChartModel()
    {
        return AuditTrail::className();
    }

    /**
     * @inheritdoc
     */
    public function getChart($request = null)
    {
        $start = $end = null;
        if($request){
            $start = Yii::$app->request->queryParams['AuditTrailSearch']['start_date'];
            $end = Yii::$app->request->queryParams['AuditTrailSearch']['end_date'];
        }
        
        return \Yii::$app->view->render('panels/trail/chart', [
            'start' => $start,
            'end' => $end, 
        ]);
    }

    /**
     * @inheritdoc
     */
    public function registerAssets($view)
    {
        GridViewAsset::register($view);
    }

    /**
     * @inheritdoc
     */
    public function cleanup($maxAge = null)
    {
        $maxAge = $maxAge !== null ? $maxAge : $this->maxAge;
        if ($maxAge === null)
            return false;
        return AuditTrail::deleteAll([
            '<=', 'created', date('Y-m-d 23:59:59', strtotime("-$maxAge days"))
        ]);
    }

}