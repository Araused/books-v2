<?php

namespace app\models\search;

use yii\data\ActiveDataProvider;
use app\models\Book;

class BookSearch extends Book
{
    public function rules(): array
    {
        return [
            [['id', 'author_id'], 'integer'],
            [['title', 'isbn', 'publish_date'], 'safe'],
        ];
    }

    public function search(array $params): ActiveDataProvider
    {
        $query = Book::find()->with('author');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 15,
            ],
            'sort' => [
                'defaultOrder' => ['id' => SORT_DESC],
            ],
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
            'author_id' => $this->author_id,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'isbn', $this->isbn])
            ->andFilterWhere(['like', 'publish_date', $publishDate]);

        return $dataProvider;
    }
}
