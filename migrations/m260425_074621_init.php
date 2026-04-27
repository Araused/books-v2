<?php

use yii\db\Migration;

class m260425_074621_init extends Migration
{
    public function safeUp(): void
    {
        $this->createTable('{{%book}}', [
            'id' => $this->primaryKey(),
            // 13 символов на сам книжный код + 4 возможных дефиса. У одной и той же книги с разными годами издания должны быть разные ISBN - поэтому поле UNIQUE
            'isbn' => $this->string(17)->notNull()->unique(),
            'title' => $this->string()->notNull(),
            'publish_date' => $this->date()->notNull(),
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

        $this->createTable('{{%book_author}}', [
            'book_id' => $this->integer()->notNull(),
            'author_id' => $this->integer()->notNull(),
        ]);

        // Составной PK позволит компактнее хранить данные и поможет исключить дубли, хотя и не так удобен как обычный
        $this->addPrimaryKey('pk-book_author', '{{%book_author}}', ['book_id', 'author_id']);

        $this->addForeignKey(
            'fk-ba-book',
            '{{%book_author}}',
            'book_id',
            '{{%book}}',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-ba-author',
            '{{%book_author}}',
            'author_id',
            '{{%author}}',
            'id',
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

        $this->createIndex(
            'idx-unique-author-phone',
            '{{%author_subscription}}',
            ['author_id', 'phone'],
            true
        );
    }

    public function safeDown(): void
    {
        $this->dropTable('{{%book_author}}');
        $this->dropTable('{{%book}}');
        $this->dropTable('{{%author_subscription}}');
        $this->dropTable('{{%author}}');
    }
}
