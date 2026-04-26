<?php

use app\models\Book;
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use yii\data\ArrayDataProvider;

/** @var $model app\models\Author */

$this->title = $model->fullName;
$this->params['breadcrumbs'][] = ['label' => 'Авторы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="author-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php if (!Yii::$app->user->isGuest): ?>
            <?= Html::a('Редактировать автора', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?php endif; ?>
        <?= Html::a('Подписаться на автора', ['subscribe', 'id' => $model->id], ['class' => 'btn btn-success']) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'lastname',
            'firstname',
            'middlename',
        ],
    ]) ?>

    <h3 class="mt-3">Книги автора</h3>

    <?= GridView::widget([
        'dataProvider' => new ArrayDataProvider([
            'allModels' => $model->books,
            'pagination' => ['pageSize' => 10],
            'sort' => [
                'attributes' => ['title', 'publish_date', 'isbn'],
                'defaultOrder' => ['publish_date' => SORT_DESC],
            ],
        ]),
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'title',
            'isbn',
            [
                'attribute' => 'publish_date',
                'format' => ['date', 'php:d.m.Y'],
            ],
            [
                'attribute' => 'image',
                'format' => 'html',
                'value' => static function (Book $book): string {
                    if ($book->image) {
                        return Html::img('@web/uploads/books/' . $book->image, ['width' => '50']);
                    }
                    return '- Нет обложки -';
                },
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'controller' => 'book',
                'template' => '{view}',
            ],
        ],
    ]); ?>

</div>