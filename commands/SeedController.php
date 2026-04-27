<?php

namespace app\commands;

use Throwable;
use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use app\models\Author;
use app\models\Book;
use app\models\AuthorSubscription;
use Faker\Factory;

class SeedController extends Controller
{
    /**
     * @throws Throwable
     */
    public function actionRun(): int
    {
        $faker = Factory::create('ru_RU');

        $db = Yii::$app->db;

        echo "Clearing tables...\n";

        $db->createCommand()->checkIntegrity(false)->execute();

        $db->createCommand()->truncateTable('{{%book_author}}')->execute();
        $db->createCommand()->truncateTable(AuthorSubscription::tableName())->execute();
        $db->createCommand()->truncateTable(Book::tableName())->execute();
        $db->createCommand()->truncateTable(Author::tableName())->execute();

        $db->createCommand()->checkIntegrity(true)->execute();

        echo "Generating fake data...\n";

        $authorIds = [];
        for ($i = 0; $i < 20; $i++) {
            $gender = rand(0, 1) ? 'male' : 'female';
            $author = new Author();

            if ($gender === 'male') {
                $author->firstname = $faker->firstNameMale();
                $author->lastname = $faker->lastNameMale();
                $author->middlename = $faker->middleNameMale();
            } else {
                $author->firstname = $faker->firstNameFemale();
                $author->lastname = $faker->lastNameFemale();
                $author->middlename = $faker->middleNameFemale();
            }

            if ($author->save()) {
                $authorIds[] = $author->id;

                for ($k = 0; $k < rand(5, 10); $k++) {
                    $sub = new AuthorSubscription();
                    $sub->author_id = $author->id;
                    $sub->phone = '+' . $faker->numerify('7##########');
                    $sub->save();
                }
            }
        }

        for ($j = 0; $j < 30; $j++) {
            $book = new Book();
            $book->title = $faker->sentence(3);
            $book->isbn = $faker->unique()->isbn13();
            $book->publish_date = $faker->date();
            $book->preview = $faker->paragraph();
            $book->image = 'default.jpg';

            if ($book->save()) {
                $randomAuthorKeys = (array) array_rand($authorIds, rand(1, 3));
                foreach ($randomAuthorKeys as $key) {
                    $author = Author::findOne($authorIds[$key]);
                    $book->link('authors', $author);
                }
            }
        }

        echo "Done!\n";
        return ExitCode::OK;
    }
}
