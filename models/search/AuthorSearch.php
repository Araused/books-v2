<?php

namespace app\models\search;

use yii\data\ActiveDataProvider;
use app\models\Author;

class AuthorSearch extends Author
{
    public function rules(): array
    {
        return [
            [['firstname', 'lastname'], 'safe'],
        ];
    }

    public function search(array $params): ActiveDataProvider
    {
        $query = Author::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 20],
            'sort' => [
                'defaultOrder' => ['lastname' => SORT_ASC],
            ],
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'lastname', $this->lastname])
            ->andFilterWhere(['like', 'firstname', $this->firstname]);

        return $dataProvider;
    }
}