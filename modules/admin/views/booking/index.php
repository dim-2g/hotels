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
    </p>


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

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
