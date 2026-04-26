<?php

/** @var $authorModel app\models\Author */
/** @var $subscriptionModel app\models\Author */

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\MaskedInput;

$this->title = 'Создать подписку на автора: ' . $authorModel->fullName;
$this->params['breadcrumbs'][] = ['label' => 'Авторы', 'url' => ['index']];

if (!Yii::$app->user->isGuest) {
    $this->params['breadcrumbs'][] = ['label' => $authorModel->fullName, 'url' => ['view', 'id' => $authorModel->id]];
}

$this->params['breadcrumbs'][] = $this->title;
?>
<div class="author-create">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="author-subscription-form">

        <?php $form = ActiveForm::begin(); ?>

        <div class="row">
            <div class="col-md-12">
                <?= $form->field($subscriptionModel, 'phone')->widget(MaskedInput::class, [
                    'mask' => '+7 (999) 999-99-99',
                    'clientOptions' => [
                        'removeMaskOnSubmit' => true,
                    ]
                ]) ?>
            </div>
        </div>

        <div class="form-group mt-3">
            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
            <?= Html::a('Отмена', ['index'], ['class' => 'btn btn-default']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>
