<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\admin\models\Manager;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\models\Condition */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="condition-form">

    <?php $form = ActiveForm::begin(); ?>


    <?= $form->field($model, 'manager_id')->dropDownList(
            Manager::find()->select(['name', 'id'])->indexBy('id')->column(),
            ['prompt'=>'Укажите менеджера']
    ) ?>

    <?= $form->field($model, 'condition')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
