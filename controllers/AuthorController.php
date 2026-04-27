<?php

namespace app\controllers;

use app\models\Book;
use Throwable;
use app\models\AuthorSubscription;
use app\models\search\AuthorSearch;
use app\models\Author;
use yii\web\Response;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

class AuthorController extends Controller
{
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['create', 'update', 'delete'],
                'rules' => [
                    [
                        'actions' => ['create', 'update', 'delete'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function actionIndex(): string
    {
        $searchModel = new AuthorSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @throws Throwable
     */
    public function actionView(int $id): string
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * @throws Throwable
     */
    public function actionSubscribe(int $id): string|Response
    {
        $authorModel = $this->findModel($id);
        $subscriptionModel = new AuthorSubscription();

        if ($this->request->isPost && $subscriptionModel->load($this->request->post())) {
            $subscriptionModel->author_id = $authorModel->id;

            if ($subscriptionModel->save()) {
                return $this->redirect(['index']);
            }
        }

        return $this->render('subscribe', [
            'authorModel' => $authorModel,
            'subscriptionModel' => $subscriptionModel,
        ]);
    }

    public function actionReport(string $year = null): string
    {
        $year = $year ?? date('Y');

        $authors = Author::find()
            ->select(['{{%author}}.*', 'COUNT({{%book_author}}.book_id) AS booksCount'])
            ->joinWith('books')
            ->where(['YEAR({{%book}}.publish_date)' => $year])
            ->groupBy('{{%author}}.id')
            ->orderBy(['booksCount' => SORT_DESC])
            ->limit(10)
            ->all();

        $years = Book::find()
            ->select('YEAR(publish_date)')
            ->distinct()
            ->orderBy(['YEAR(publish_date)' => SORT_DESC])
            ->column();

        return $this->render('report', [
            'authors' => $authors,
            'years' => $years,
            'year' => $year,
        ]);
    }

    /**
     * @throws Throwable
     */
    public function actionCreate(): string|Response
    {
        $model = new Author();

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * @throws Throwable
     */
    public function actionUpdate(int $id): string|Response
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @throws Throwable
     */
    public function actionDelete(int $id): Response
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * @throws Throwable
     */
    protected function findModel(int $id): Author
    {
        if (($model = Author::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Запрошенная страница не существует.');
    }
}

