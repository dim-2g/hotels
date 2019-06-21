<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\models\Condition */

$this->title = 'Update Condition: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Conditions', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="condition-update">

    <h1><?= Html::encode($this->title) ?></h1>
    <p>
        <?= Html::a('<span class="glyphicon glyphicon-arrow-left"></span> К списку', ['index'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
