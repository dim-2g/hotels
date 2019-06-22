<?php

namespace app\controllers;

use app\models\Dictionary\CountryDictionary;
use app\modules\admin\models\Manager;
use LibUiTourFilter\assets\libs\DotAsset;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
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

    public function actionTest()
    {
        //$data = json_decode(Booking::findOne(57)->raw_data, true);
        $data = Booking::find()->where(['id' => 61])->limit(1)->asArray()->one();

        $manager = BookingController::findManagerForCustomForm($data);
        if ($manager) {
            $managerName = Manager::findOne($manager)->name;
            echo "Будет назначен менеджер $managerName";
            die();
        }
        die('Не удалось определить менеджера');
    }

    public function actionBooking()
    {
        $booking = Booking::find()->where(['id' => 114])->one();
        debug($booking->extended->date_from);
        foreach ($booking->directions as $direction) {
            if ($direction->countryProfile) {
                echo "<p><b>Страна:</b> {$direction->countryProfile->name}</p>";
            }
            if ($direction->cityProfile) {
                echo "<p><b>Город:</b> {$direction->cityProfile->name}</p>";
            }
            if ($direction->departmentCityProfile) {
                echo "<p><b>Город вылета:</b> {$direction->departmentCityProfile->name}</p>";
            }
            //debug($direction);
        }
        die('..');
        $booking = Booking::find()->where(['id' => 136])->one();
        foreach ($booking->hotels as $hotel) {
            echo "<p><b>Отель:</b> {$hotel->hotelProfile->name}</p>";
            echo "<p><b>Страна:</b> {$hotel->hotelProfile->resortProfile->countryProfile->name}</p>";
            echo "<p><b>Город:</b> {$hotel->hotelProfile->resortProfile->name}</p>";
            echo "<p><b>Звездность:</b> {$hotel->hotelProfile->categoryProfile->name}</p>";
        }

        die('...');
    }

}