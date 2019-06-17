<?php

namespace app\controllers;

use app\models\CountryDictionary;
use app\modules\admin\models\Manager;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\Booking;

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
            ->where(['<', 'id', 100])
            ->asArray()
            ->orderBy(['name' => SORT_ASC])
            ->all();

        return $this->render('index', [
            'data' => $formData
        ]);
    }

    public function actionTest()
    {
        $data = json_decode(Booking::findOne(56)->raw_data, true);
        /*
        echo '<pre>';
        print_r($data);
        echo '</pre>';
        */
        $manager = BookingController::findRightManager($data);
        if ($manager) {
            $managerName = Manager::findOne($manager)->name;
            echo "Будет назначен менеджер $managerName";
            die();
        }
        die('Не удалось определить менеджера');
    }

}