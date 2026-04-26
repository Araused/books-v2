<?php

namespace app\models;

use app\behaviors\NotificationBehavior;
use Yii;
use yii\base\Exception;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\web\UploadedFile;
use yii\helpers\FileHelper;
use app\behaviors\FlashBehavior;

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
    const IMAGE_PATH_PREFIX = '@web/uploads/books/';
    public UploadedFile|string|null $imageFile = null;

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
        ];
    }

    public function rules(): array
    {
        return [
            [['author_id', 'isbn', 'title', 'publish_date'], 'required'],
            [['publish_date'], 'string'],
            [['author_id'], 'integer'],
            [['isbn'], 'unique'],
            [['isbn'], 'string', 'max' => 17],
            [['title', 'preview'], 'string', 'max' => 255],
            [
                ['author_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Author::class,
                'targetAttribute' => ['author_id' => 'id'],
            ],
            [['imageFile'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg'],
        ];
    }

    /**
     * @throws Exception
     */
    public function upload(): bool
    {
        if ($this->validate(['imageFile']) && $this->imageFile) {
            $path = Yii::getAlias('@webroot/uploads/books/');

            FileHelper::createDirectory($path);

            $fileName = uniqid() . '.' . $this->imageFile->extension;

            if ($this->imageFile->saveAs($path . $fileName)) {
                if ($this->image && file_exists($path . $this->image)) {
                    unlink($path . $this->image);
                }

                $this->image = $fileName;

                return true;
            }
        }

        return false;
    }

    public function getAuthor(): ActiveQuery
    {
        return $this->hasOne(Author::class, ['id' => 'author_id']);
    }
}
