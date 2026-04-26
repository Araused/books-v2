<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use app\models\Author;
use app\models\Book;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var app\models\search\BookSearch $searchModel */

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
                'filter' => Author::getAuthorsList(),
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
                        return Html::img(Book::IMAGE_PATH_PREFIX . $book->image, ['width' => '50']);
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
