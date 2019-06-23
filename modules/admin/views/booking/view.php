<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
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
            'wish:ntext',
            [
                    'attribute' => 'manager_id',
                    'value' => function($data) {
                        $output = [];
                        $managerText = 'нет распределения,<br /> так как нет направления';
                        if (!empty($data->manager_id)) {
                            $manager = Manager::find()->where(['id' => $data->manager_id])->limit(1)->one();
                            $managerText = $manager->name;
                        }
                        $output[] = "<b>Менеджер</b>:<br /> {$managerText}";
        /*
                        $extendedFields = json_decode($data->raw_data, true);
                        $output = [];
                        $managerText = 'нет распределения,<br /> так как нет направления';
                        if (!empty($data->manager_id)) {
                            $manager = Manager::find()->where(['id' => $data->manager_id])->limit(1)->one();
                            $managerText = $manager->name;
                        }
                        $output[] = "<b>Менеджер</b>:<br /> {$managerText}";
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
        */
                        return implode('<br />', $output);
                    },
                    'format' => 'raw'
            ],
            [
                    'attribute' => 'date_from',
                    'value' => function($data) {
                        return 'date_from';
                        //return $data->extended->date_from;
                    },
            ]
        ],
    ]) ?>

</div>
