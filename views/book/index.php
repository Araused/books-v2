<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use app\models\Author;
use app\models\Book;

/** @var yii\web\View $this */
/** @var app\models\search\BookSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Книги';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="book-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <p><?= Html::a('Добавить книгу', ['create'], ['class' => 'btn btn-success']) ?></p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'title',
            [
                'attribute' => 'author_id',
                'value' => static function (Book $model): string {
                    return $model->author->fullName;
                },
                'filter' => ArrayHelper::map(
                    // asArray с вызовом статического метода-склейки должен быть менее требователен к памяти чем загрузка объектами
                    Author::find()->asArray()->all(),
                    'id',
                    static function ($model): string {
                        return Author::authorFullName($model);
                    }
                ),
            ],
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
            ],
        ],
    ]); ?>
</div>
