<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\modules\admin\models\Manager;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Conditions';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="condition-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Condition', ['create'], ['class' => 'btn btn-success']) ?>
    </p>


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            [
                'attribute' => 'manager_id',
                'value' => function($data) {
                    $manager = Manager::find()->select(['name', 'id', 'email'])
                        ->where(['id' => $data->manager_id])
                        ->one();
                    return "{$manager->name} (id: {$manager->id}, {$manager->email})";
                },
            ],
            'condition:ntext',


            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
