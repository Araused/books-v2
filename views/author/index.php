<?php

use app\models\Author;
use yii\grid\GridView;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\search\AuthorSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Список авторов';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="author-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php if (!Yii::$app->user->isGuest): ?>
            <?= Html::a('Добавить автора', ['create'], ['class' => 'btn btn-success']) ?>
        <?php endif; ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'lastname',
            'firstname',
            'middlename',

            [
                'label' => 'Книг в базе',
                'value' => static function (Author $model): string {
                    return count($model->books);
                }
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'header' => 'Действия',
                'template' => '{view} {subscribe} {update} {delete}',
                'buttons' => [
                    'subscribe' => static function (string $url, Author $model): string {
                        return Html::a(
                            '<i class="fa-solid fa-bell"></i>',
                            ['/author/subscribe', 'id' => $model->id],
                            [
                                'title' => 'Подписаться на автора',
                                'class' => '',
                            ]
                        );
                    },
                ],
                'visibleButtons' => [
                    'update' => !Yii::$app->user->isGuest,
                    'delete' => !Yii::$app->user->isGuest,
                ],
            ],
        ],
    ]) ?>

</div>