<?php

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\web\UploadedFile;
use app\behaviors\FlashBehavior;
use app\behaviors\NotificationBehavior;
use app\behaviors\BookAuthorsBehavior;
use app\behaviors\UploadedFileBehavior;

/**
 * @property int $id
 * @property string $isbn
 * @property string $title
 * @property string $publish_date
 * @property string|null $preview
 * @property string|null $image
 *
 * @property Author $authors
 */
class Book extends ActiveRecord
{
    const IMAGE_PATH_PREFIX = '@web/uploads/books/';

    public UploadedFile|string|null $imageFile = null;
    public array|string|null $authorIds = null;

    public static function tableName(): string
    {
        return '{{%book}}';
    }

    public function behaviors(): array
    {
        return [
            'flash' => [
                'class' => FlashBehavior::class,
                'savedMessage' => 'Книга успешно сохранена.',
                'deletedMessage' => 'Книга успешно удалена.',
            ],
            'notification' => [
                'class' => NotificationBehavior::class,
            ],
            'linkAuthors' => [
                'class' => BookAuthorsBehavior::class,
            ],
            'uploadedFile' => [
                'class' => UploadedFileBehavior::class,
                'attribute' => 'image',
                'fileAttribute' => 'imageFile',
                'folder' => 'books',
            ],
        ];
    }

    public function rules(): array
    {
        return [
            [['isbn', 'title', 'publish_date', 'authorIds'], 'required'],
            [['publish_date'], 'string'],
            [['isbn'], 'unique'],
            [['isbn'], 'string', 'max' => 17],
            [['title', 'preview'], 'string', 'max' => 255],
            [['imageFile'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg'],
            [['authorIds'], 'each', 'rule' => ['integer']],
        ];
    }

    public function getAuthors(): ActiveQuery
    {
        return $this->hasMany(Author::class, ['id' => 'author_id'])
            ->viaTable('{{%book_author}}', ['book_id' => 'id']);
    }
}
