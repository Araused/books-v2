<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\ActiveQuery;
use app\jobs\SendNotificationJob; // Предполагаемый путь к джобе

/**
 * @property int $id
 * @property int $author_id
 * @property string $isbn
 * @property string $title
 * @property string $publish_date
 * @property string|null $preview
 * @property string|null $image
 *
 * @property Author $author
 */
class Book extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%book}}';
    }

    public function rules(): array
    {
        return [
            [['author_id', 'isbn', 'title', 'publish_date'], 'required'],
            [['author_id'], 'integer'],
            [['publish_date'], 'safe'],
            [['isbn'], 'string', 'max' => 17],
            [['title', 'preview', 'image'], 'string', 'max' => 255],
            [['isbn'], 'unique'],
            [
                ['author_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Author::class,
                'targetAttribute' => ['author_id' => 'id'],
            ],
        ];
    }

    public function getAuthor(): ActiveQuery
    {
        return $this->hasOne(Author::class, ['id' => 'author_id']);
    }

    public function afterSave($insert, $changedAttributes): void
    {
        parent::afterSave($insert, $changedAttributes);

        if ($insert && isset(Yii::$app->queue)) {
            Yii::$app->queue->push(new SendNotificationJob([
                'bookId' => $this->id,
                'authorId' => $this->author_id,
            ]));
        }
    }
}