<?php

namespace app\jobs;

use Yii;
use yii\base\BaseObject;
use yii\queue\JobInterface;
use app\models\Book;
use app\models\AuthorSubscription;

class SendNotificationJob extends BaseObject implements JobInterface
{
    public int $bookId;

    public function execute($queue): void
    {
        $book = Book::find()
            ->where(['id' => $this->bookId])
            ->with('author')
            ->one();

        if (!$book) {
            Yii::error(
                "Ошибка очереди: Книга с ID {$this->bookId} не найдена.",
                'notifications'
            );

            return;
        }

        $author = $book->author;

        $subscriptions = AuthorSubscription::find()
            ->where(['author_id' => $author->id])
            ->all();

        if (empty($subscriptions)) {
            Yii::info(
                "Подписчиков для автора {$author->fullName} нет. Рассылка отменена.",
                'notifications'
            );

            return;
        }

        foreach ($subscriptions as $sub) {
            $message = "У автора {$author->fullName} вышла книга: «{$book->title}»";

            $this->sendSms($sub->phone, $message);
        }
    }

    protected function sendSms(string $phone, string $message): void
    {
        // @TODO: Здесь можно имплементировать подключение по API к какому-нибудь сервису реальной отправки СМС
        Yii::info(
            "SMS отправлено на номер {$phone}: {$message}",
            'notifications'
        );
    }
}
