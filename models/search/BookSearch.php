<?php

namespace app\models\search;

use yii\data\ActiveDataProvider;
use app\models\Book;

class BookSearch extends Book
{
    public ?int $authorFilter = null;

    public function rules(): array
    {
        return [
            [['id', 'authorFilter'], 'integer'],
            [['title', 'isbn', 'publish_date'], 'safe'],
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = Book::find()->joinWith(['authors']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 15],
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]],
        ]);

        $this->load($params);

        // Немного конвертаций с датой для простоты
        $publishDate = $this->publish_date
            ? date("Y-m-d", strtotime($this->publish_date))
            : null;

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            '{{%book_author}}.author_id' => $this->authorFilter,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'isbn', $this->isbn])
            ->andFilterWhere(['like', 'publish_date', $publishDate]);

        return $dataProvider;
    }
}
