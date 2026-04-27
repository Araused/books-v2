<?php

use yii\widgets\DetailView;
use yii\helpers\Html;
use app\models\Book;

/* @var $this yii\web\View */
/* @var $model app\models\Book */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Книги', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="book-view">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы уверены, что хотите удалить эту книгу?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <div class="row">
        <div class="col-md-4">
            <?php if ($model->image): ?>
                <?= Html::img(Book::IMAGE_PATH_PREFIX . $model->image, ['class' => 'img-thumbnail', 'style' => 'width:100%']) ?>
            <?php else: ?>
                <div class="alert alert-info">- Нет обложки -</div>
            <?php endif; ?>
        </div>
        <div class="col-md-8">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    [
                        'label' => 'Автор',
                        'format' => 'raw',
                        'value' => static function (Book $model): string {
                            $links = [];

                            foreach ($model->authors as $author) {
                                $links[] = Html::a(
                                    $author->fullName,
                                    ['/author/view', 'id' => $author->id]
                                );
                            }

                            return implode('<br>', $links);
                        },
                    ],
                    'isbn',
                    'publish_date:date',
                    'preview:ntext',
                ],
            ]) ?>
        </div>
    </div>
</div>
