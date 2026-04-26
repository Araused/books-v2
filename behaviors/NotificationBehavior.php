<?php

namespace app\behaviors;

use Yii;
use yii\base\Behavior;
use yii\console\Application;
use yii\db\BaseActiveRecord;

class NotificationBehavior extends Behavior
{
    public string $phoneAttribute = 'phone';

    public function events(): array
    {
        return [
            BaseActiveRecord::EVENT_AFTER_INSERT => 'sendNotification',
        ];
    }

    public function sendNotification(): void
    {
        if (!Yii::$app instanceof Application && isset(Yii::$app->queue)) {
            Yii::$app->queue->push(new SendNotificationJob([
                'bookId' => $this->owner->id,
                'authorId' => $this->owner->author_id,
            ]));
        }
    }
}
