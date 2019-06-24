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
            'id',
            'created_at:ntext',
            [
                'attribute' => 'parametrs',
                'value' => function($data) {
                    if ($data->type == 'custom') {
                        return $data->parametrs;
                    }
                    if ($data->type == 'tours') {
                        $output = [];
                        $iter = 1;
                        foreach ($data->directions as $direction) {
                            $output[] = "{$iter}. {$direction->countryName} / {$direction->cityName}";
                            $iter++;
                        }
                        return implode("<br />", $output);
                    }
                    if ($data->type == 'hotels') {
                        $output = [];
                        $iter = 1;
                        foreach ($data->hotels as $hotel) {
                            $output[] = "{$iter}. {$hotel->countryName} / {$hotel->cityName} / {$hotel->name}";
                            $iter++;
                        }
                        return implode("<br />", $output);
                    }
                },
                'format' => 'raw',
            ],
            'name:ntext',
            'email:ntext',
            'phone:ntext',
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
                    'format' => 'raw',
            ],
            [
                'attribute' => 'wish',
                'value' => function($data) {
                    if ($data->type == 'custom') {
                        return $data->extended['wish'];
                    }
                    if ($data->type == 'tours') {
                        $output = [];
                        $iter = 1;
                        foreach ($data->directions as $direction) {
                            $row = [];
                            $row[] = "Город вылета: {$direction->departmentCityName}";
                            $row[] = "Звездность: {$direction->starsString}";
                            $row[] = "Рейтинг: {$direction->ratingString}";
                            $row[] = "Питание: {$direction->mealsString}";
                            if ($direction->placeString) {
                                $row[] = "Расположение: {$direction->placeCategoryName} {$direction->placeString}";
                            }
                            if ($direction->forBabyString) {
                                $row[] = "Для детей: {$direction->forBabyString}";
                            }
                            if ($direction->otherString) {
                                $row[] = "Прочее: {$direction->otherString}";
                            }
                            $output[] = "{$iter}." . implode(' / ', $row);
                            $iter++;
                        }
                        if (!empty($data->extended['wish'])) {
                            $output[] = "<b>Доп.пожелания:</b><br /> {$data->extended['wish']}";
                        }
                        return implode("<br />", $output);
                    }
                    if ($data->type == 'hotels') {
                        $output = [];
                        $output[] = "Город вылета: {$data->extended['departmentCityName']}";
                        $output[] = "Питание: {$data->mealsString}";
                        $iter = 1;
                        foreach ($data->hotels as $hotel) {
                            $row = [];
                            $row[] = "Звездность: {$hotel->stars}";
                            $output[] = "{$iter}." . implode(' / ', $row);
                            $iter++;
                        }
                        if (!empty($data->extended['wish'])) {
                            $output[] = "<b>Доп.пожелания:</b><br /> {$data->extended['wish']}";
                        }
                        return implode("<br />", $output);
                    }
                },
                'format' => 'raw',
            ],
            [
                    'attribute' => 'extended',
                    'value' => function($data) {
                        $output = [];
                        if ($data->type == 'tours' || $data->type == 'hotels') {
                            $output[] = "<b>Дата вылета</b>:<br /> {$data->extended['date_from']}&nbsp;-&nbsp;{$data->extended['date_to']}";
                            $output[] = "<b>Кол-во ночей</b>:<br /> {$data->extended['night_from']}&nbsp;-&nbsp;{$data->extended['night_to']}";
                            if ($data->extended['child']) {
                                $childs = [];
                                foreach (['child_age_1', 'child_age_2', 'child_age_3'] as $child) {
                                    if (!empty($data->extended[$child])) {
                                        $childs[] = $data->extended[$child];
                                    }
                                }
                            }
                            $childString = '';
                            if (!empty($childs) && count($childs) > 0) {
                                $childString = ' ('. implode(', ', $childs).' лет)';
                            }
                            $output[] = "<b>Кол-во человек</b>:<br /> взр.: {$data->extended['adult']}, детей: {$data->extended['child']}{$childString}";
                            $output[] = "<b>Бюджет</b>:<br /> {$data->extended['price_comfort']}&nbsp;-&nbsp;{$data->extended['price_max']} {$data->extended['currencyString']}";
                            $output[] = "<b>Город туриста</b>:<br /> {$data->touristCityName}";
                        }
                        return implode('<br />', $output);
                    },
                    'format' => 'raw'
            ],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    </div>


</div>
