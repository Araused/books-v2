<?php

namespace app\models;

use yii\db\ActiveRecord;
use yii\db\ActiveQuery;
use app\behaviors\FlashBehavior;
use yii\helpers\ArrayHelper;

/**
 * @property int $id
 * @property string $firstname
 * @property string $lastname
 * @property string|null $middlename
 *
 * @property string $fullName
 *
 * @property Book[] $books
 * @property AuthorSubscription[] $subscriptions
 */
class Author extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%author}}';
    }

    public function behaviors(): array
    {
        return [
            'flash' => [
                'class' => FlashBehavior::class,
                'savedMessage' => 'Автор успешно сохранен.',
                'deletedMessage' => 'Автор успешно удален.',
            ],
        ];
    }

    public function rules(): array
    {
        return [
            [['firstname', 'lastname'], 'required'],
            [['firstname', 'lastname', 'middlename'], 'string', 'max' => 255],
        ];
    }

    public function getFullName(): string
    {
        return self::authorFullName($this);
    }

    public function getBooks(): ActiveQuery
    {
        return $this->hasMany(Book::class, ['author_id' => 'id']);
    }

    public function getSubscriptions(): ActiveQuery
    {
        return $this->hasMany(AuthorSubscription::class, ['author_id' => 'id']);
    }

    public static function authorFullName(array|self $model): string {
        return $model['firstname']
            . ($model['middlename'] ? ' ' . $model['middlename'] : '')
            . ' ' . $model['lastname'];
    }

    public static function getAuthorsList(): array
    {
        return ArrayHelper::map(
            Author::find()->asArray()->all(),
            'id',
            static function ($model): string {
                return Author::authorFullName($model);
            }
        );
    }
}
