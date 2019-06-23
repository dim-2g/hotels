<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\modules\admin\models\Condition;
use app\models\Dictionary\CountryDictionary;
use app\models\Dictionary\CityDictionary;
use app\models\Dictionary\AlloccatDictionary;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Консультанты';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="manager-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Manager', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('<span class="glyphicon glyphicon-usd"></span> Заказы',
            ['/admin'],
            ['class' => 'btn btn-default']) ?>
        <?= Html::a('<span class="glyphicon glyphicon-cog"></span> Критерии',
            ['/admin/condition/index'],
            ['class' => 'btn btn-default']) ?>
    </p>


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'name:ntext',
            'email:ntext',
            [
                    'attribute' => 'condition',
                    'value' => function($data) {
                        $conditionRows = Condition::find()->where(['manager_id' => $data->id])
                                                       ->all();
                        //$conditions = json_decode($condition, true);
                        $output = [];
                        foreach ($conditionRows as $item) {
                            $conditionArray = json_decode($item->condition, true);
                            $outputRow = [];
                            foreach ($conditionArray as $conditionKey => $conditionValue) {
                                switch ($conditionKey) {
                                    case 'country_id':
                                        $conditionValue = CountryDictionary::findName($conditionValue);
                                        break;
                                    case 'city_id':
                                        $conditionValue = CityDictionary::findName($conditionValue);
                                        break;
                                    case 'alloccat_id':
                                        $conditionValue = AlloccatDictionary::findName($conditionValue);
                                        break;
                                }
                                if (array_key_exists($conditionKey, Condition::$fieldName)) {
                                    $conditionKey = Condition::$fieldName[$conditionKey];
                                }
                                $outputRow[] = "$conditionKey = $conditionValue";
                            }
                            $output[] = implode(' и ', $outputRow);
                           // $label = $item->condition;
                            //$output[] = $label;
                        }
                        return implode('<br />или<br />', $output);
                    },
                    'format' => 'raw'
            ],
            'active',
            'general',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
