<?php

namespace app\behaviors;

use app\models\Author;
use Yii;
use yii\base\Behavior;
use yii\db\BaseActiveRecord;
use yii\helpers\ArrayHelper;

class BookAuthorsBehavior extends Behavior
{
    public function events(): array
    {
        return [
            BaseActiveRecord::EVENT_AFTER_INSERT => 'linkAuthors',
            BaseActiveRecord::EVENT_AFTER_UPDATE => 'linkAuthors',
            BaseActiveRecord::EVENT_AFTER_FIND => 'setCurrentAuthors',
        ];
    }

    public function setCurrentAuthors(): void
    {
        $this->owner->authorIds = ArrayHelper::getColumn($this->owner->authors, 'id');
    }

    public function linkAuthors(): void
    {
        $this->owner->unlinkAll('authors', true);

        if (is_array($this->owner->authorIds)) {
            foreach ($this->owner->authorIds as $id) {
                $author = Author::findOne($id);

                if ($author) {
                    $this->owner->link('authors', $author);
                }
            }
        }
    }
}
