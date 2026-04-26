<?php

namespace app\models;

use yii\db\ActiveRecord;
use yii\db\ActiveQuery;
use app\behaviors\FlashBehavior;

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
        return $this->firstname
            . ($this->middlename ? ' ' . $this->middlename : '')
            . ' ' . $this->lastname;
    }

    public function getBooks(): ActiveQuery
    {
        return $this->hasMany(Book::class, ['author_id' => 'id']);
    }

    public function getSubscriptions(): ActiveQuery
    {
        return $this->hasMany(AuthorSubscription::class, ['author_id' => 'id']);
    }
}
