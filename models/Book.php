<?php

namespace app\models;

use Yii;
use yii\base\Exception;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\web\UploadedFile;
use yii\helpers\FileHelper;
use app\behaviors\FlashBehavior;
use app\behaviors\NotificationBehavior;

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
    public ?array $authorIds = null;

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
            [['isbn', 'title', 'publish_date'], 'required'],
            [['publish_date'], 'string'],
            [['isbn'], 'unique'],
            [['isbn'], 'string', 'max' => 17],
            [['title', 'preview'], 'string', 'max' => 255],
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

    public function getAuthors(): ActiveQuery
    {
        return $this->hasMany(Author::class, ['id' => 'author_id'])
            ->viaTable('{{%book_author}}', ['book_id' => 'id']);
    }
}
