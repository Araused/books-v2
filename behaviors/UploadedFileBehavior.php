<?php

namespace app\behaviors;

use Throwable;
use Yii;
use yii\base\Behavior;
use yii\db\BaseActiveRecord;
use yii\helpers\FileHelper;
use yii\web\UploadedFile;

class UploadedFileBehavior extends Behavior
{
    public string $attribute;
    public string $fileAttribute;
    public string $folder;

    public function events(): array
    {
        return [
            BaseActiveRecord::EVENT_BEFORE_VALIDATE => 'beforeValidate',
            BaseActiveRecord::EVENT_BEFORE_INSERT => 'beforeSave',
            BaseActiveRecord::EVENT_BEFORE_UPDATE => 'beforeSave',
            BaseActiveRecord::EVENT_AFTER_DELETE => 'afterDelete',
        ];
    }

    public function beforeValidate(): void
    {
        $this->owner->{$this->fileAttribute} = UploadedFile::getInstance($this->owner, $this->fileAttribute);
    }

    /**
     * @throws Throwable
     */
    public function beforeSave(): void
    {
        $file = $this->owner->{$this->fileAttribute};

        if ($file instanceof UploadedFile) {
            $path = Yii::getAlias("@webroot/uploads/{$this->folder}/");

            FileHelper::createDirectory($path);

            $oldFile = $this->owner->getOldAttribute($this->attribute);

            if ($oldFile && file_exists($path . $oldFile)) {
                unlink($path . $oldFile);
            }

            $filename = uniqid() . '.' . $file->extension;

            if ($file->saveAs($path . $filename)) {
                $this->owner->{$this->attribute} = $filename;
            }
        }
    }

    public function afterDelete()
    {
        $path = Yii::getAlias("@webroot/uploads/{$this->folder}/");
        $file = $this->owner->{$this->attribute};

        if ($file && file_exists($path . $file)) {
            unlink($path . $file);
        }
    }
}
