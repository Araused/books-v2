<?php

namespace app\models;

use yii\db\ActiveRecord;
use yii\db\ActiveQuery;

/**
 * @property int $id
 * @property string $firstname
 * @property string $lastname
 * @property string|null $middlename
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

    public function rules(): array
    {
        return [
            [['firstname', 'lastname'], 'required'],
            [['firstname', 'lastname', 'middlename'], 'string', 'max' => 255],
        ];
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
