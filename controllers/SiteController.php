<?php

namespace app\controllers;

use app\models\Dictionary\CountryDictionary;
use app\models\Params;
use app\modules\admin\models\Condition;
use app\modules\admin\models\Manager;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\Booking;
use app\helpers\BookingHelper;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        //TODO: проверить, нужны ли все данные для контролов, так как они не все параметры подхватывают
        $formData = [];
        $dateNow = new \DateTime('+14day');
        $formData['dateFrom'] = $dateNow->format('Y-m-d');
        $dateNow->modify('+7day');
        $formData['dateTo'] = $dateNow->format('Y-m-d');

        $formData['nightFrom'] = 7;
        $formData['nightTo'] = 14;

        $formData['adults'] = 2;
        $formData['children'] = 1;

        $formData['countries'] = CountryDictionary::find()
            ->asArray()
            ->orderBy(['name' => SORT_ASC])
            ->all();

        return $this->render('index', [
            'data' => $formData
        ]);
    }
}