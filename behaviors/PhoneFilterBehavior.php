<?php

namespace app\behaviors;

use Yii;
use yii\base\Behavior;
use yii\base\Model;

class PhoneFilterBehavior extends Behavior
{
    public string $phoneAttribute = 'phone';

    public function events(): array
    {
        return [
            Model::EVENT_BEFORE_VALIDATE => 'filterPhone',
        ];
    }

    public function filterPhone(): void
    {
        if ($this->owner->{$this->phoneAttribute}) {
            $this->owner->{$this->phoneAttribute} = preg_replace('/\D/', '', $this->owner->{$this->phoneAttribute});

            if (str_starts_with($this->owner->{$this->phoneAttribute}, '8') && strlen($this->owner->{$this->phoneAttribute}) === 11) {
                $this->owner->{$this->phoneAttribute} = '7' . substr($this->owner->{$this->phoneAttribute}, 1);
            }

            if (strlen($this->owner->{$this->phoneAttribute}) === 10) {
                $this->owner->{$this->phoneAttribute} = '7' . $this->owner->{$this->phoneAttribute};
            }

            if (!str_starts_with($this->owner->{$this->phoneAttribute}, '+')) {
                $this->owner->{$this->phoneAttribute} = '+' . $this->owner->{$this->phoneAttribute};
            }
        }
    }
}
