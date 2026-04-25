<?php

use yii\db\Migration;

class m260425_074621_init extends Migration
{
    public function safeUp(): void
    {
        $this->createTable('{{%book}}', [
            'id' => $this->primaryKey(),
            'author_id' => $this->integer()->notNull(),
            // 13 символов на сам книжный код + 4 возможных дефиса. У одной и той же книги с разными годами издания должны быть разные ISBN - поэтому поле UNIQUE
            'isbn' => $this->string(17)->notNull()->unique(),
            'title' => $this->string()->notNull(),
            'publish_date' => $this->dateTime()->notNull(),
            'preview' => $this->string(),
            'image' => $this->string(),
        ]);

        $this->createTable('{{%author}}', [
            'id' => $this->primaryKey(),
            'firstname' => $this->string()->notNull(),
            'lastname' => $this->string()->notNull(),
            'middlename' => $this->string(),
        ]);

        $this->createTable('{{%author_subscription}}', [
            'id' => $this->primaryKey(),
            'author_id' => $this->integer()->notNull(),
            // Для удобства хранения стандартных российских сотовых номеров: 11 цифр вместе с плюсом в начале
            'phone' => $this->string(12)->notNull(),
        ]);

        $this->addForeignKey(
            'fk_book_to_author',
            '{{%book}}',
            'author_id',
            '{{%author}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk_subscription_to_author',
            '{{%author_subscription}}',
            'author_id',
            '{{%author}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        // Хотя приложение тестовое, в реальном были бы нужны примерно такие индексы для более оптимальной работы выборки основных данных:

        $this->createIndex('idx-book-title', '{{%book}}', 'title');
        $this->createIndex('idx-book-publish_date', '{{%book}}', 'publish_date');

        $this->createIndex('idx-author-full_name', '{{%author}}', ['lastname', 'firstname']);
    }

    public function safeDown(): void
    {
        $this->dropTable('{{%book}}');
        $this->dropTable('{{%author_subscription}}');
        $this->dropTable('{{%author}}');
    }
}
