<?php

namespace app\jobs;

use Yii;
use yii\base\BaseObject;
use yii\queue\JobInterface;
use app\models\Book;
use app\models\AuthorSubscription;

class SendNotificationJob extends BaseObject implements JobInterface
{
    /**
     * ID созданной книги (передается при создании объекта)
     */
    public $bookId;

    /**
     * Основная логика задания
     * @param \yii\queue\Queue $queue объект очереди
     */
    public function execute($queue)
    {
        // 1. Находим книгу со всеми данными автора (используем жадную загрузку)
        $book = Book::find()
            ->where(['id' => $this->bookId])
            ->with('author')
            ->one();

        if (!$book) {
            Yii::error("Ошибка очереди: Книга с ID {$this->bookId} не найдена.", 'notifications');
            return;
        }

        $author = $book->author;

        // 2. Ищем всех подписчиков этого автора
        $subscriptions = AuthorSubscription::find()
            ->where(['author_id' => $author->id])
            ->all();

        if (empty($subscriptions)) {
            Yii::info("Подписчиков для автора {$author->lastname} нет. Рассылка отменена.", 'notifications');
            return;
        }

        // 3. Рассылаем уведомления
        foreach ($subscriptions as $sub) {
            $message = "Новинка! У автора {$author->lastname} вышла книга: «{$book->title}»";

            // Здесь должна быть интеграция с реальным SMS-шлюзом
            // Пока просто пишем в лог для проверки
            $this->sendSms($sub->phone, $message);
        }
    }

    /**
     * Имитация отправки SMS
     */
    protected function sendSms($phone, $message)
    {
        // В реальном проекте здесь будет вызов API (например, Twilio, SMS.ru и т.д.)
        Yii::info("SMS отправлено на номер {$phone}: {$message}", 'notifications');
    }
}
