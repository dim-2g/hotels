<?php

use app\controllers\BookingController;
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use app\modules\admin\models\Manager;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\models\Booking */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Bookings', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="booking-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('<span class="glyphicon glyphicon-arrow-left"></span> К списку', ['index'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
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
        ],
    ]) ?>

    <? if (!empty($extended)) { ?>

    <h2>Данные расширенной формы</h2>

        <?= DetailView::widget([
            'model' => $extended,
            'attributes' => [
                'date_from:ntext',
                'date_to:ntext',
                'night_from:ntext',
                'night_to:ntext',
                'adult:ntext',
                'child:ntext',
                'child_age_1:ntext',
                'child_age_2:ntext',
                'child_age_3:ntext',
                'price_comfort:ntext',
                'price_max:ntext',
                [
                        'attribute' => 'currency',
                        'value' => function($data) {
                            return $data->currencyString;
                        }
                ],
                'wish:ntext',
                [
                    'attribute' => 'department_city_id',
                    'value' => function($data) {
                        return $data->departmentCityName;
                    }
                ],
            ],
        ]) ?>

    <? } ?>

    <? if (!empty($directions)) { ?>

        <h2>Данные по Турпакетам</h2>

        <?php foreach ($directions as $key => $direction) {?>
            <p>#<?=($key+1)?></p>
        <?= DetailView::widget([
            'model' => $direction,
            'attributes' => [
                [
                    'attribute' => 'country_id',
                    'value' => function($data) {
                        return $data->countryName;
                    }
                ],
                [
                    'attribute' => 'city_id',
                    'value' => function($data) {
                        return $data->cityName;
                    }
                ],
                [
                    'attribute' => 'department_city_id',
                    'value' => function($data) {
                        return $data->departmentCityName;
                    }
                ],
                [
                    'attribute' => 'params',
                    'value' => function($data) {
                        $output = [];
                        if ($data->mealsString) {
                            $output[] = "<p><b>Питание</b>: {$data->mealsString}</p>";
                        }
                        if ($data->placeCategoryName) {
                            $output[] = "<p><b>Расположение</b>: {$data->placeCategoryName} {$data->placeString}</p>";
                        }
                        if ($data->starsString) {
                            $output[] = "<p><b>Звездность</b>: {$data->starsString}</p>";
                        }
                        if ($data->ratingString) {
                            $output[] = "<p><b>Рейтинг</b>: {$data->ratingString}</p>";
                        }
                        if ($data->forBabyString) {
                            $output[] = "<p><b>Для детей</b>: {$data->forBabyString}</p>";
                        }
                        if ($data->otherString) {
                            $output[] = "<p><b>Прочее</b>: {$data->otherString}</p>";
                        }
                        return implode("\n", $output);
                    },
                    'format' => 'raw'
                ],
            ],
        ]) ?>
        <?php } ?>

    <? } ?>

    <? if (!empty($hotels)) { ?>

        <h2>Данные по конкретным отелям</h2>

        <?php foreach ($hotels as $key => $hotel) {?>
            <p>#<?=($key+1)?></p>
            <?= DetailView::widget([
                'model' => $hotel,
                'attributes' => [
                    [
                        'attribute' => 'hotel_id',
                        'value' => function($data) {
                            return "{$data->countryName} / {$data->cityName} / {$data->name} / {$data->stars}*";
                        }
                    ],
                ],
            ]) ?>
        <?php } ?>

    <? } ?>

</div>
