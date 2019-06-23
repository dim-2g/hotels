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
        $equalFields = [
            'country_id' => '=',
            'city_id' => '=',
            'alloccat_id' => 'IN',
        ];

        $manager = BookingController::findManagerToOrder(67);
        debug($manager);
        die();
        //получим все условия и найдем точное совпадение
        $booking = Booking::find()->where(['id' => 235])->one();

        $conditions = Condition::findAllConditions();
        if ($booking->id && $booking->type == 'tours') {
            $hasCountry = false;
            foreach ($booking->directions as $direction) {
                //проверяем каждое направление по всем критериям
                foreach ($conditions as $conditionItem) {
                    $countEquals = 0;
                    //проверяем по каждому условию
                    foreach ($conditionItem['condition'] as $criterionKey => $criterionValue) {
                        $value = $direction->findValue($criterionKey);
                        if (BookingHelper::isEqual($criterionValue, $value)) {
                            $countEquals++;
                        }
                    }
                    //если кол-во критериев и кол-во совпадений равно, то назначаем менеджера
                    if (count($conditionItem['condition']) == $countEquals) {
                        return $conditionItem['manager_id'];
                    }
                }
                if (!empty($direction->findValue('country_id'))) {
                    $hasCountry = true;
                }

            }
            //если не подошел ни один менеджер, то проверим наличие страны и назначим Главного
            if ($hasCountry) {
                $generalManager = Manager::find()->where(['general' => 1])->limit(1)->one();
                if (!empty($generalManager->id)) {
                    return $generalManager->id;
                }
            }

        }

        die('stop');
        //$params = Params::find()->asArray()->where(['entity' => 'booking_directions', 'entity_id' => 19])->andWhere(['category' => 'tour_category'])->all();
        //debug($params);
        //die();
        //debug($params->createCommand()->getRawSql());

        $booking = Booking::find()->with('directions')->with('hotels')->where(['id' => 228])->one();
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
            echo "<p>Питания</p>";
            foreach ($direction->meals as $item) {
                //var_dump($item->value);
                var_dump($item->valueText);
            }
            echo "<p>Расположение</p>";
            echo "<p>{$direction->placeCategory->valueText}</p>";
            foreach ($direction->place as $item) {
                var_dump($item->valueText);
            }

            echo "<p>Звездность</p>";
            foreach ($direction->categories as $item) {
                var_dump($item->valueText);
            }

            echo "<p>Рейтинг</p>";
            foreach ($direction->rating as $item) {
                var_dump($item->valueText);
            }

            echo "<p>Для детей</p>";
            foreach ($direction->forBaby as $item) {
                var_dump($item->valueText);
            }

            echo "<p>Прочее</p>";
            foreach ($direction->other as $item) {
                var_dump($item->valueText);
            }
            //var_dump($direction->meals);
            //foreach ($direction->getMeals() as $item) {
            //    debug($item->value);
           // }

        }

        $booking = Booking::find()->with('hotels')->where(['id' => 228])->one();
        if ($booking->type = 'hotels') {

        }
        foreach ($booking->hotels as $hotel) {
            echo "<p><b>Отель:</b> {$hotel->hotelProfile->name} : {$hotel->name}</p>";
            echo "<p><b>Страна:</b> {$hotel->hotelProfile->resortProfile->countryProfile->name} : {$hotel->countryName}</p>";
            echo "<p><b>Город:</b> {$hotel->hotelProfile->resortProfile->name} : {$hotel->cityName}</p>";
            echo "<p><b>Звездность:</b> {$hotel->hotelProfile->categoryProfile->value} : {$hotel->stars}</p>";
        }


    }

}