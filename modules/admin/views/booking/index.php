<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\modules\admin\models\Manager;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Заказы';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="booking-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Booking', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('<span class="glyphicon glyphicon-user"></span> Консультанты',
                    ['/admin/manager/index'],
                    ['class' => 'btn btn-default']) ?>

    </p>

    <div class="booking-table">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'name:ntext',
            'email:ntext',
            'phone:ntext',
            'parametrs:ntext',
            'created_at:ntext',
            [
                    'attribute' => 'manager_id',
                    'value' => function($data) {
                        $managerText = 'нет распределения,<br /> так как нет направления';
                        if (!empty($data->manager_id)) {
                            $manager = Manager::find()->where(['id' => $data->manager_id])->limit(1)->one();
                            $managerText = $manager->name;
                        }
                        return $managerText;
                    },
                    'format' => 'raw'
            ],
            'wish:ntext',
            [
                    'attribute' => 'extended',
                    'value' => function($data) {
                        $extendedFields = json_decode($data->raw_data, true);
                        $output = [];
                        $output[] = "<b>Дата вылета</b>:<br /> {$extendedFields['general']['df']}&nbsp;-&nbsp;{$extendedFields['general']['dt']}";
                        $output[] = "<b>Кол-во ночей</b>:<br /> {$extendedFields['general']['nf']}&nbsp;-&nbsp;{$extendedFields['general']['nt']}";
                        $childs = [];
                        foreach (['ch1' , 'ch2', 'ch3'] as $child) {
                            if (!empty($extendedFields['general'][$child])) {
                                $childs[] = $extendedFields['general'][$child];
                            }
                        }
                        $childString = '';
                        if (!empty($childs) && count($childs) > 0) {
                            $childString = ' ('. implode(', ', $childs).' лет)';
                        }
                        $output[] = "<b>Кол-во человек</b>:<br /> взр.: {$extendedFields['general']['ad']}, детей: {$extendedFields['general']['ch']}{$childString}";
                        $output[] = "<b>Бюджет</b>:<br /> {$data->budget}";
                        $output[] = "<b>Город туриста</b>:<br /> {$data->tourist_city}";
                        return implode('<br />', $output);
                    },
                    'format' => 'raw'
            ],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    </div>


</div>
