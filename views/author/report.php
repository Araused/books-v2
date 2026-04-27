<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $authors app\models\Author[] */
/* @var $years array */
/* @var $year string */

$this->title = "ТОП-10 авторов за $year год";
?>

<div class="author-report">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="well">
        <?php $form = ActiveForm::begin(['method' => 'get', 'action' => ['report']]); ?>
        <div class="row">
            <div class="col-md-3">
                <?= Html::label('Выберите год') ?>
                <?= Html::dropDownList(
                    'year',
                    $year,
                    array_combine(
                        $years,
                        $years
                    ),
                    ['class' => 'form-control']
                ) ?>
            </div>
            <div class="col-md-2">
                <br>
                <?= Html::submitButton('Показать', ['class' => 'btn btn-primary']) ?>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Автор</th>
                <th>Количество книг за год</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($authors as $index => $author): ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= Html::a($author->fullName, ['view', 'id' => $author->id]) ?></td>
                    <td><strong><?= $author->booksCount ?></strong></td>
                </tr>
            <?php endforeach; ?>

            <?php if (empty($authors)): ?>
                <tr>
                    <td colspan="3" class="text-center text-muted">За этот год книг не найдено.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
