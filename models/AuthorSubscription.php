<?php

namespace app\models;

use yii\db\ActiveRecord;
use yii\db\ActiveQuery;
use app\behaviors\FlashBehavior;

/**
 * @property int $id
 * @property int $author_id
 * @property string $phone
 *
 * @property Author $author
 */
class AuthorSubscription extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%author_subscription}}';
    }


    public function behaviors(): array
    {
        return [
            'flash' => [
                'class' => FlashBehavior::class,
                'savedMessage' => 'Подписка на новые книги автора успешно создана.',
                // Текст удаления не нужен, т.к. удалять подписки исходя из ТЗ не придется
            ],
        ];
    }

    public function rules(): array
    {
        return [
            [['!author_id', 'phone'], 'required'],
            [['!author_id'], 'integer'],
            [['phone'], 'string', 'max' => 12],
            [['phone'], 'filter', 'filter' => static function (string $value): string {
                return preg_replace('/[^\d+]/', '', $value);
            }],
            [
                ['author_id', 'phone'],
                'unique',
                'targetAttribute' => ['author_id', 'phone'],
                'message' => 'Этот номер телефона уже подписан на данного автора.',
            ],
            [
                ['!author_id'],
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
}
