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
                        $managerText = 'нет распределения,<br /> так как нет направления';
                        if (!empty($data->manager_id)) {
                            $manager = Manager::find()->where(['id' => $data->manager_id])->limit(1)->one();
                            $managerText = $manager->name;
                        }
                        return $managerText;
                    }
            ],
        ],
    ]) ?>

</div>
