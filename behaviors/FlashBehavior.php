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
        Yii::$app->session->setFlash('success', $this->savedMessage);
    }


    public function setFlashDeleted(): void
    {
        Yii::$app->session->setFlash('success', $this->deletedMessage);
    }
}
