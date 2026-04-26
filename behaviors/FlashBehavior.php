<?php

namespace app\behaviors;

use Yii;
use yii\base\Behavior;
use yii\db\BaseActiveRecord;

class FlashBehavior extends Behavior
{
    public string $savedMessage = 'Данные сохранены.';
    public string $deletedMessage = 'Данные удалены.';

    public function events(): array
    {
        return [
            BaseActiveRecord::EVENT_AFTER_INSERT => 'setFlashSaved',
            BaseActiveRecord::EVENT_AFTER_UPDATE => 'setFlashSaved',
            BaseActiveRecord::EVENT_AFTER_DELETE => 'setFlashDeleted',
        ];
    }

    public function setFlashSaved(): void
    {
        if (Yii::$app->has('session')) {
            Yii::$app->session->setFlash('success', $this->savedMessage);
        }
    }

    public function setFlashDeleted(): void
    {
        if (Yii::$app->has('session')) {
            Yii::$app->session->setFlash('danger', $this->deletedMessage);
        }
    }
}
