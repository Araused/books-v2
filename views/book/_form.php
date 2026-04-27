<?php

use app\models\Book;
use app\models\Author;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model app\models\Book */
?>

<div class="book-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'authorIds')->widget(Select2::class, [
                'data' => Author::getAuthorsList(),
                'options' => [
                    'placeholder' => '- Выберите авторов -',
                    'multiple' => true,
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'tags' => true,
                    'tokenSeparators' => [',', ' '],
                ],
            ]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'isbn')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'publish_date')->input('date') ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'imageFile')->fileInput(['accept' => 'image/*']) ?>
            <?php if ($model->image): ?>
                <div class="well">
                    Текущая обложка: <?= Html::img(Book::IMAGE_PATH_PREFIX . $model->image, ['width' => '100']) ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?= $form->field($model, 'preview')->textarea(['rows' => 4]) ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить книгу', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Отмена', ['index'], ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
