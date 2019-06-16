<?php

namespace app\controllers;

use app\models\AlloccatDictionary;
use app\models\AllocPlaceTypeDictionary;
use app\models\AllocPlaceValueDictionary;
use app\models\CityDictionary;
use app\models\CountryDictionary;
use app\models\HotelDictionary;
use Yii;
use yii\web\Controller;
use app\models\Booking;
use app\helpers\BookingHelper;

class BookingController extends Controller
{
    public static $currency = [
        0 => 'р.',
        1 => 'дол.',
        2 => 'евро',
        3 => 'евро',
        4 => 'гр.',
        5 => 'бел.р.',
        6 => 'тен.',
    ];
    public static $childrenParams = [
        'potty' => 'Детский горшок',
        'meal' => 'Детские блюда',
        'changing_table' => 'Пеленальный столик',
        'animation' => 'Анимация',
    ];
    public static $otherParams = [
        'animation' => 'Веселая анимация',
        'parties' => 'Тусовки рядом с отелем',
    ];

    public function beforeAction($action)
    {
        if ($action->id == 'custom') {
            $this->enableCsrfValidation = false;
        }
        return parent::beforeAction($action);
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    /*
     * Обрабатываем поступающий ajax запрос
     * Возвращаем json
     * success = true в случае успеха
     * success = false если есть ошибки
     * errors - массив ошибок (поле => описание ошибки)
     */
    public function actionCustom()
    {
        $response = [
            'success' => false,
            'errors' => [],
        ];

        $booking = new Booking();
        if ($booking->load(Yii::$app->request->post(), '')) {
            if ($booking->validate()) {
                $booking->save();
                BookingHelper::sendMail($booking, 'dim-2g@yandex.ru');
                $response['success'] = true;
            } else {
                $response['errors'] = BookingHelper::prepareErrorsAjaxForm($booking->getErrors());
            }
        }
        return json_encode($response);
    }

    /*
     * Обрабатываем поступающий ajax запрос со сложной формы
     * Возвращаем json
     * success = true в случае успеха
     * success = false если есть ошибки
     * errors - массив ошибок (поле => описание ошибки)
     */
    public function actionStore()
    {
        $response = [
            'success' => false,
            'errors' => [],
            'data' => [],
        ];

        $post = Yii::$app->request->post();
        $orderData = [];
        $orderData['name'] = 'не указано';
        $orderData['phone'] = 'не указано';
        $orderData['date_departure'] = self::prepareDateDeparture($post);
        $orderData['persons'] = self::preparePersons($post);
        $orderData['budget'] = self::prepareBudget($post);
        $orderData['parametrs'] = self::prepareDirection($post);
        $orderData['wish'] = self::prepareWish($post);

        $booking = new Booking();
        if ($booking->load($orderData, '')) {
            if ($booking->validate()) {
                $booking->save();
                //BookingHelper::sendMail($booking, 'dim-2g@yandex.ru');
                $response['success'] = true;
                $response['data']['id'] = $booking->id;
            } else {
                $response['errors'] = BookingHelper::prepareErrorsAjaxForm($booking->getErrors());
            }
        }
        return json_encode($response);
    }

    public static function sendMail($bookingRecord, $emailTo)
    {
        $message = [];
        $message[] = "Поступила заявка № {$bookingRecord->id}";
        $message[] = "Страна, курорт, отель: {$bookingRecord->parametrs}";
        $message[] = "Имя: {$bookingRecord->name}";
        $message[] = "Телефон: {$bookingRecord->phone}";
        $message[] = "Email {$bookingRecord->email}";

        return Yii::$app->mailer->compose('views/booking', [
            'orderNumber' => $bookingRecord->id,
            'orderName' => $bookingRecord->name,
            'orderPhone' => $bookingRecord->phone,
            'orderParametrs' => $bookingRecord->parametrs,
            'briefLink' => Url::to(['admin/booking/view', 'id' => $bookingRecord->id], true),
            'briefList' => Url::to(['admin/booking'], true),
            'emailTo' => $emailTo,
            'supportLink' => 'https://tophotels.ru/feedback',
        ])
            ->setFrom('hotels@modxguru.ru')
            ->setTo($emailTo)
            ->setSubject('Добавлена новая заявка')
            ->send();
    }

    /*
     * Преобразуем данные для поля Дата вылета
     * возвращает строку вида "01.01.19 + 5 дн / 7-14 нч."
     */
    public static function prepareDateDeparture($postData)
    {
        $output = [];
        $dateFrom = \DateTime::createFromFormat('Y-m-d', $postData['general']['df']);
        $dateTo = \DateTime::createFromFormat('Y-m-d', $postData['general']['dt']);
        $days = $dateFrom->diff($dateTo);
        $output[] = $dateFrom->format('d.m.y') . ' + ' . $days->format('%a дн.');

        $nightFrom = $postData['general']['nf'];
        $nightTo = $postData['general']['nt'];
        if ($nightFrom != $nightTo) {
            $output[] = $nightFrom . '-' . $nightTo . ' нч.';
        } else {
            $output[] = $nightFrom . ' нч.';
        }
        return implode(' / ', $output);
    }

    /*
     * Преобразуем данные для поля Кол-во человек
     * возвращает строку вида "1 взр. + 3 реб. (1,2,3 лет)"
     */
    public static function preparePersons($postData)
    {
        $output = [];
        //добавляем взрослых
        $output[] = $postData['general']['ad'] . ' взр.';
        //добавляем детей
        $childCount = $postData['general']['ch'];
        if ($childCount > 0) {
            $childAges = [];
            foreach (['ch1', 'ch2', 'ch3'] as $age) {
                if (!empty($postData['general'][$age])) {
                    $childAges[] = $postData['general'][$age];
                }
            }
            asort($childAges);
            $output[] = $postData['general']['ch'] . ' реб. (' . implode(',', $childAges) . ' лет)';
        }
        return implode(' + ', $output);
    }

    /*
     * Преобразуем данные для поля Бюджет
     * возвращает строку вида "50 000 р" или "20 000 евро"
     */
    public static function prepareBudget($postData)
    {
        $output = [];
        $currencyString = static::$currency[$postData['general']['cur']];
        $priceFrom = static::priceFormat($postData['general']['pc']);
        $output[] = $priceFrom;
        if ($postData['general']['pt'] != 1000000) {
            $priceMax = static::priceFormat($postData['general']['pt']);
            $output[] = $priceMax;
        }
        return implode(' - ', $output) . ' ' . $currencyString;
    }

    /*
     * Возвращает отформатированную строку для цен
     */
    public static function priceFormat($price)
    {
        return number_format($price, 0, '', ' ' );
    }

    /*
     * Преобразуем данные для поля Направление
     * возвращаем пронумерованные строки вида "1. Страна / Курорт(город) / Отель"
     */
    public static function prepareDirection($postData)
    {
        $output = [];
        //если присутствуют данные по турпакетам
        if ($postData['params']['order_type'] == 'tours' && array_key_exists('tour', $postData)) {
            $output[] = 'Данные по Турпакетам';
            $iter = 1;
            foreach ($postData['tour']['items'] as $item) {
                //если active=0, значит строка была скрыта/удалена, поэтому пропустим ее
                if ($item['active'] == 0) continue;
                $countryName = $cityName = '';
                if (!empty($item['countryId'])) {
                    $countryName = static::findCountryNameById($item['countryId']);
                }
                if (!empty($item['cityId'])) {
                    $cityName = static::findCityNameById($item['cityId']);
                }
                $output[] = "$iter. $countryName / $cityName";
                $iter++;
            }
        }
        //если присутствуют данные по Конкретным отелям
        if ($postData['params']['order_type'] == 'hotel' && array_key_exists('hotels', $postData)) {
            $output[] = 'Данные по Конкретным отелям';
            $iter = 1;
            foreach ($postData['hotels']['items'] as $item) {
                //если active=0, значит строка была скрыта/удалена, поэтому пропустим ее
                if ($item['active'] == 0) continue;
                if (!empty($item['hotelId'])) {
                    $hotel = HotelDictionary::find()
                        ->with('resort')
                        ->where(['id' => $item['hotelId']])
                        ->asArray()
                        ->one();
                    $cityName = $hotel['resort']['name'];
                    $countryName = static::findCountryNameById($hotel['resort']['country']);
                    $hotelName = $hotel['name'];
                    $output[] = "$iter. $countryName / $cityName / $hotelName";
                }
            }
        }

        return implode("\n", $output);
    }

    /*
     * Получение названия страны по id
     */
    public static function findCountryNameById($id)
    {
        $countryName = CountryDictionary::find()
            ->select('name')
            ->where(['id' => $id])
            ->asArray()
            ->one();
        return $countryName['name'];
    }

    /*
     * Получение названия города по id
     */
    public static function findCityNameById($id)
    {
        $cityName = CityDictionary::find()
            ->select('name')
            ->where(['id' => $id])
            ->asArray()
            ->one();
        return $cityName['name'];
    }

    /*
     * Преобразуем данные для поля Пожелания
     */
    public static function prepareWish($postData)
    {
        $output = [];
        //если присутствуют данные по турпакетам
        if ($postData['params']['order_type'] == 'tours' && array_key_exists('tour', $postData)) {
            $output[] = 'Данные по Турпакетам';
            $iter = 1;
            foreach ($postData['tour']['items'] as $item) {
                //если active=0, значит строка была скрыта/удалена, поэтому пропустим ее
                if ($item['active'] == 0) continue;
                $outputRow = [];

                //получаем Город вылета
                if (!empty($item['departmentId'])) {
                    $departmentCityName = static::findCityNameById($item['departmentId']);
                } else {
                    $departmentCityName = 'не указан';
                }
                $outputRow[] = 'Город вылета ' . $departmentCityName;

                //получаем Звездность отеля
                if (!empty($item['params']['tour_category'])) {
                    $alloccat = AlloccatDictionary::find()
                        ->select('name')
                        ->where(['id' => $item['params']['tour_category'] ])
                        ->asArray()
                        ->all();
                    $tmp = [];
                    foreach ($alloccat as $alloccatItem) {
                        $tmp[] = $alloccatItem['name'];
                    }
                    $outputRow[] = 'Звездность ' . implode(',', $tmp);
                }

                //Получаем Рейтинг
                if (!empty($item['params']['tour_rating'])) {
                    foreach ($item['params']['tour_rating'] as $tourRating) {
                        if ($tourRating == 'not_important') {
                            $tourRating = 'не важен';
                        }
                        $outputRow[] = 'Рейтинг ' . $tourRating;
                    }
                }

                //Получаем Питание
                if (!empty($item['params']['tour_meal'])) {
                    $tmp = [];
                    foreach ($item['params']['tour_meal'] as $tourMeal) {
                        if ($tourMeal == 'any') {
                            $tourMeal = 'Любое питание';
                        }
                        $tmp[] = $tourMeal;
                    }
                    $outputRow[] = 'Питание ' . implode(',', $tmp);
                }

                //Получаем Расположение
                if (!empty($item['params']['tour_place'])) {
                    $tmp = [];
                    foreach ($item['params']['tour_place'] as $tourPlace) {
                        if ($tourPlace == 'any') {
                            $tourPlace = 'Любой тип';
                        } else {
                            list($tourPlaceCategory, $tourPlaceId) = explode('_' , $tourPlace);
                        }
                        $tourPlace = AllocPlaceValueDictionary::find()
                            ->select('name')
                            ->where(['id' => $tourPlaceId, 'place' => $tourPlaceCategory])
                            ->asArray()
                            ->one();
                        $tmp[] = $tourPlace['name'];
                    }
                    $placeCategory = AllocPlaceTypeDictionary::find()
                        ->select('name')
                        ->where(['id' => $tourPlaceCategory])
                        ->asArray()
                        ->one();
                    $outputRow[] = 'Расположение ' . $placeCategory['name'] . ' ' . implode(',', $tmp);
                }

                //Параметры для детей
                if (!empty($item['params']['tour_baby'])) {
                    $tmp = [];
                    foreach ($item['params']['tour_baby'] as $tourBaby) {
                        $tmp[] = static::$childrenParams[$tourBaby];
                    }
                    $outputRow[] = 'Для детей ' . implode(',', $tmp);
                }

                //Прочие
                if (!empty($item['params']['tour_other'])) {
                    $tmp = [];
                    foreach ($item['params']['tour_other'] as $tourOther) {
                        $tmp[] = static::$otherParams[$tourOther];
                    }
                    $outputRow[] = 'Прочие ' . implode(',', $tmp);
                }

                $output[] = "$iter. " . implode(' / ', $outputRow);
                $iter++;
            }
        }
        //если присутствуют данные по Конкретным отелям
        if ($postData['params']['order_type'] == 'hotel' && array_key_exists('hotels', $postData)) {
            $output[] = 'Данные по Конкретным отелям';
            $iter = 1;

            $outputRow = [];
            //получаем Город вылета
            if (!empty($postData['hotels']['departmentId'])) {
                $departmentCityName = static::findCityNameById($postData['hotels']['departmentId']);
            } else {
                $departmentCityName = 'не указан';
            }
            $outputRow[] = 'Город вылета ' . $departmentCityName;

            //Получаем Питание
            if (!empty($postData['hotels']['meal'])) {
                $tmp = [];
                foreach ($postData['hotels']['meal'] as $tourMeal) {
                    if ($tourMeal == 'any') {
                        $tourMeal = 'Любое питание';
                    }
                    $tmp[] = $tourMeal;
                }
                $outputRow[] = 'Питание ' . implode(',', $tmp);
            }

            //Получаем звездность по первому отелю
            $hotelStar = '';
            foreach ($postData['hotels']['items'] as $item) {
                //если active=0, значит строка была скрыта/удалена, поэтому пропустим ее
                if ($item['active'] == 0) continue;
                if (!empty($item['hotelId'])) {
                    $hotel = HotelDictionary::find()
                        ->with('category')
                        ->where(['id' => $item['hotelId']])
                        ->asArray()
                        ->one();
                    $hotelStar = $hotel['category']['name'];
                    break;
                }
            }
            $outputRow[] = 'Звездность ' . $hotelStar;
            //собираем воедино
            $output[] = implode(' / ', $outputRow);
        }

        return implode("\n", $output);
    }

}
