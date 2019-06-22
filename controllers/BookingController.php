<?php

namespace app\controllers;

use app\models\BookingDirections;
use app\models\BookingHotels;
use app\models\BookingExtended;
use app\models\Dictionary\AlloccatDictionary;
use app\models\Dictionary\AllocPlaceTypeDictionary;
use app\models\Dictionary\AllocPlaceValueDictionary;
use app\models\Dictionary\CityDictionary;
use app\models\Dictionary\CountryDictionary;
use app\models\Dictionary\HotelDictionary;
use app\modules\admin\models\Condition;
use app\modules\admin\models\Manager;
use Yii;
use yii\helpers\Url;
use yii\web\Controller;
use app\models\Booking;
use app\models\BookingExt;
use app\helpers\BookingHelper;

class BookingController extends Controller
{
    /*
     * Данные для Валют
     * В базе postgre нет этих данных. В своей базе не стал заводить
     */
    public static $currency = [
        0 => 'р.',
        1 => 'дол.',
        2 => 'евро',
        3 => 'руб',
        4 => 'гр.',
        5 => 'бел.р.',
        6 => 'тен.',
    ];

    /*
     * Данные для Параметры отеля - Детское
     * В базе postgre нет этих данных. В своей базе не стал заводить
     */
    public static $childrenParams = [
        'potty' => 'Детский горшок',
        'meal' => 'Детские блюда',
        'changing_table' => 'Пеленальный столик',
        'animation' => 'Анимация',
    ];

    /*
     * Данные для Параметры отеля - Прочее
     * В базе postgre нет этих данных. В своей базе не стал заводить
     */
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
        $post = Yii::$app->request->post();
        $managerId = self::findManagerForCustomForm($post);
        $post['manager_id'] = $managerId ? $managerId : '';

        $booking = new Booking();
        if ($booking->load($post, '')) {
            if ($booking->validate()) {
                $booking->save();
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
        $errors = [];

        $post = Yii::$app->request->post();
        $orderData = self::findFieldsFromPost($post);
        $scenario = self::findScenario($post);
        $orderData['type'] = $scenario;

        $booking = new Booking(['scenario' => Booking::SCENARIO_FIRST_STEP]);
        $bookingExtended = new BookingExtended(['scenario' => $scenario]);
/*
        $transaction = Booking::getDb()->beginTransaction();
        try {
*/
            /*
             * сохраняем простую форму, чтобы получить id для связи с расширенной, а также
             * дальнейшего заполнения на втором шаге
             */
            if ($booking->load($orderData, '')) {
                if ($booking->validate()) {
                    $booking->save();
                    $response['data']['id'] = $booking->id;
                }
            }
            $errors['simple'] = $booking->getErrors();
            /*
             * Сохраняем расширенную форму
             */
            if (!empty($booking->id)) {
                $orderData['booking_id'] = $booking->id;
                if ($bookingExtended->load($orderData, '')) {
                    if ($bookingExtended->validate()) {
                        $bookingExtended->save();
                        //$transaction->commit();
                    }
                }
                $errors['extended'] = $bookingExtended->getErrors();
            }
            /*
             * Если форма содержит Направления, сохраняем их
             */
            if (self::hasTours($post) && !empty($booking->id)) {
                foreach ($post['tour']['items'] as $iter => $item) {
                    if (empty($item['active'])) continue;
                    $direction = new BookingDirections();
                    $orderDirections = self::findFieldsForDirections($item);
                    $orderDirections['booking_id'] = $booking->id;
                    if ($direction->load($orderDirections, '')) {
                        if ($direction->validate()) {
                            $direction->save();
                        }
                    }
                    $errors['direction_' . $iter] = $direction->getErrors();
                }
            }
            /*
             * Если форма содержит Направления, сохраняем их
             */
            if (self::hasHotels($post)  && !empty($booking->id)) {
                foreach ($post['hotels']['items'] as $iter => $item) {
                    if (empty($item['active'])) continue;
                    $hotels = new BookingHotels();
                    $orderHotels = self::findFieldsForHotels($item);
                    $orderHotels['booking_id'] = $booking->id;
                    if ($hotels->load($orderHotels, '')) {
                        if ($hotels->validate()) {
                            $hotels->save();
                        }
                    }
                    $errors['hotels_' . $iter] = $hotels->getErrors();
                }
            }
/*
        } catch (\Exception $e) {
            $response['data'] = [];
            $transaction->rollBack();
        }
*/
        foreach ($errors as $blockError) {
            if (count($blockError) > 0) {
                $response['errors'] = array_merge(
                    $response['errors'],
                    BookingHelper::prepareErrorsAjaxForm($blockError)
                );
            }
        }
        if (count($response['errors']) == 0) {
            $response['success'] = true;
        }

        return json_encode($response);
    }

    /**
     * @return string
     */
    public static function findScenario($postData)
    {
        if (!empty($postData['params']['order_type'])) {
            if ($postData['params']['order_type'] == 'tours') {
                return BookingExtended::SCENARIO_TOURS;
            }
            if ($postData['params']['order_type'] == 'hotel') {
                return BookingExtended::SCENARIO_HOTELS;
            }
        }

        return 'default';
    }

    public static function findFieldsForDirections($post)
    {
        $orderDirections = [];
        $orderDirections['country_id'] = (!empty($post['countryId'])) ? $post['countryId'] : null;
        $orderDirections['city_id'] = (!empty($post['cityId'])) ? $post['cityId'] : null;
        $orderDirections['department_city_id'] = (!empty($post['departmentId'])) ? $post['departmentId'] : null;
        $orderDirections['params'] = '!!!!!!!!!!';

        return $orderDirections;
    }

    /**
     * @return array
     */
    public static function findFieldsForHotels($post)
    {
        $orderHotels = [];
        $orderHotels['hotel_id'] = (!empty($post['hotelId'])) ? $post['hotelId'] : null;

        return $orderHotels;
    }

    public static function findFieldsFromPost($post)
    {
        $orderData = [];
        $orderData['date_from'] = self::findFieldByKeys($post, 'general', 'df');
        $orderData['date_to'] = self::findFieldByKeys($post, 'general', 'dt');
        $orderData['night_from'] = self::findFieldByKeys($post, 'general', 'nf');
        $orderData['night_to'] = self::findFieldByKeys($post, 'general', 'nt');
        $orderData['adult'] = self::findFieldByKeys($post, 'general', 'ad');
        $orderData['child'] = self::findFieldByKeys($post, 'general', 'ch');
        $orderData['child_age_1'] = self::findFieldByKeys($post, 'general', 'ch1');
        $orderData['child_age_2'] = self::findFieldByKeys($post, 'general', 'ch2');
        $orderData['child_age_3'] = self::findFieldByKeys($post, 'general', 'ch3');
        $orderData['price_comfort'] = self::findFieldByKeys($post, 'general', 'pc');
        $orderData['price_max'] = self::findFieldByKeys($post, 'general', 'pt');
        $orderData['wish'] = self::findFieldByKeys($post, 'params', 'wish');
        $orderData['department_city_id'] = self::findFieldByKeys($post, 'hotels', 'departmentId');
        if (!empty($post['hotels']['meal'])) {
            $orderData['meal'] = implode(',', $post['hotels']['meal']);
        }

        return $orderData;
    }

    public static function findFieldByKeys($postData, $keyParentArray, $keyChildArray)
    {
        if (isset($postData[$keyParentArray][$keyChildArray])) {
            return $postData[$keyParentArray][$keyChildArray];
        } else {
            return '';
        }
    }

    public static function findFieldDateFrom($postData)
    {
        return $postData['general']['df'];
    }

    /*
     * Добавляем данные к существующему заказу
     */
    public function actionStoreAdd()
    {
        $response = [
            'success' => false,
            'errors' => [],
            'data' => [],
        ];
        $post = Yii::$app->request->post();
        $booking = Booking::findOne($post['order_id']);
        $booking->scenario = $booking->type;
        $booking->name = $post['name'];
        $booking->phone = $post['phone'];
        $booking->email = $post['email'];
        $booking->tourist_city_id = $post['tourist_city_id'];

        if ($booking->validate()) {
            $booking->save();
            $response['data']['id'] = $booking->id;
            $response['success'] = true;
        }

        if (!$response['success']) {
            $response['errors'] = BookingHelper::prepareErrorsAjaxForm($booking->getErrors());
        }

        return json_encode($response);


        if (!empty($post['tourist_city'])) {
            $post['tourist_city'] = self::findCityNameById($post['tourist_city']);
        } else {
            $post['tourist_city'] = '';
        }
        $booking = BookingExt::findOne($post['order_id']);
        $booking->tourist_city = $post['tourist_city'];
        $booking->name = $post['name'];
        $booking->phone = $post['phone'];
        $booking->email = $post['email'];
        $savedRawData = json_decode($booking->raw_data, true);
        $savedRawData = array_merge($savedRawData, $post);
        $booking->raw_data = json_encode($savedRawData);
        if ($booking->validate()) {
            $booking->save();
            $response['success'] = true;
        } else {
            $response['errors'] = BookingHelper::prepareErrorsAjaxForm($booking->getErrors());
        }

        return json_encode($response);
    }

    /*
     * Отправка письма
     * @param $bookingRecord - экземпляр АР модели Booking
     * @param string $emailTo - email адрес получателя
     * return bool
     */
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
     * Отправка письма
     * @param $bookingRecord - экземпляр АР модели Booking
     * @param $manager - экземпляр АР модели Manager, если не передавать, то будет выбираться из заявки
     * return bool
     */
    public static function sendMailEx($bookingRecord, $manager = null)
    {
        if (empty($manager)) {
            $manager = Manager::find()->where(['id' => $bookingRecord->manager_id])->one();
            if (empty($manager)) {
                return false;
            }
        }
        $message = [];
        $message[] = "Поступила заявка № {$bookingRecord->id}";
        $message[] = "Страна, курорт, отель: {$bookingRecord->parametrs}";
        $message[] = "Имя: {$bookingRecord->name}";
        $message[] = "Телефон: {$bookingRecord->phone}";
        $message[] = "Email {$bookingRecord->email}";
        $emailTo = $manager->email;
        return Yii::$app->mailer->compose('views/booking', [
            'managerName' => $manager->name,
            'orderNumber' => $bookingRecord->id,
            'orderName' => $bookingRecord->name,
            'orderPhone' => $bookingRecord->phone,
            'orderParametrs' => $bookingRecord->parametrs,
            'briefLink' => Url::to(['admin/booking/view', 'id' => $bookingRecord->id], true),
            'briefList' => Url::to(['admin/booking'], true),
            'emailTo' => $emailTo,
            'supportLink' => 'https://tophotels.ru/feedback',
        ])
            ->setFrom(Yii::$app->params['senderEmail'])
            ->setTo($emailTo)
            ->setSubject('Добавлена новая заявка')
            ->send();
    }

    /*
     * Поиск менеджерапо данный из формы Нестандартный подбор
     * @param $postData - POST данные с формы
     * return id менеджера
     */
    public static function findManagerForCustomForm($postData)
    {
        //получим все условия и найдем точное совпадение
        $conditions = Condition::findAllConditions();
        //попробуем найти заявки с Нестандартной формы с вхождением по Городу и Стране.
        if ($manager = self::findManagerInCustomFormByCountryCity($postData, $conditions)) {
            return $manager;
        }

        //получим список всех стран и попробуем найти вхождение.
        //если найдется, то назначим general менеджеру
        $generalManager = Manager::find()->where(['general' => 1])->limit(1)->one();
        if (!empty($generalManager->id)) {
            if (self::findManagerInCustomFormByCountries($postData)) {
                return $generalManager->id;
            }
        }

        return false;
    }

    /*
     * Проверяет соответствие массивов Критерия и Претендента по массиву Правил
     * @param $equalFileds - массив Парвил
     * @param $conditionArray - массив критериев из АР Condition
     * @param $orderArray - массив с проверяемыми данными
     * return id менеджера
     */
    private static function isEqualFields($equalFileds, $conditionArray, $orderArray)
    {
        $equalCount = 0;
        $equalValue = 0;
        //проходим по всем условиям
        foreach ($conditionArray as $key => $equalField) {
            //важно чтобы ключ содержался и в первом и во втором массиве
            $method = '=';
            if (isset($equalFileds[$key])) {
                $method = $equalFileds[$key];
            }
            // проверяем есть ли такой ключ  в проверяемом массиве
            if (!isset($orderArray[$key])) {
                continue;
            }
            $equalCount++;

            switch ($method) {
                case '=':
                    if ($conditionArray[$key] == $orderArray[$key]) {
                        $equalValue++;
                    }
                    break;
                case 'IN':
                    if (in_array($conditionArray[$key], $orderArray[$key])) {
                        $equalValue++;
                    }
                    break;
            }
        }
        return $equalCount == $equalValue;
    }

    /**
     * Комплексный метод подбора подходящего менеджера
     * @param $postData - данные с формы
     * return id менеджера либо false
     */
    public static function findRightManager($postData)
    {
        $equalFields = [
            'country' => '=',
            'city' => '=',
            'stars' => 'IN',
        ];

        //получим все условия и найдем точное совпадение
        $conditions = Condition::findAllConditions();
        if ($manager = self::findManagerInTourRows($postData, $conditions, $equalFields)) {
            return $manager;
        }
        if ($manager = self::findManagerInHotelRows($postData, $conditions, $equalFields)) {
            return $manager;
        }
        //если не нашли, то проверим, есть ли вхождение Страны в заявке, чтобы отправит General менеджеру
        //получим менеджера
        $generalManager = Manager::find()->where(['general' => 1])->limit(1)->one();
        if (!empty($generalManager->id)) {
            if (self::hasCountryInTourRows($postData)) {
                return $generalManager->id;
            }
            if (self::hasCountryInHotelsRows($postData)) {
                return $generalManager->id;
            }
        }

        return false;
    }

    /*
     * Проверяем, есть ли в направениях данные по стране
     * @param $postData - данные с формы
     */
    private static function hasCountryInTourRows($postData)
    {
        if (self::hasTours($postData)) {
            foreach ($postData['tour']['items'] as $item) {
                //если active=0, значит строка была скрыта/удалена, поэтому пропустим ее
                if (empty($item['active'])) continue;
                if (!empty($item['countryId'])) {
                    return true;
                }
            }
        }

        return false;
    }

    private static function isOrderTour()
    {

    }

    /*
     * Проверяем, есть ли данные по стране в строках с отелями
     * @param $postData - данные с формы
     */
    private static function hasCountryInHotelsRows($postData)
    {
        if (self::hasHotels($postData)) {
            foreach ($postData['hotels']['items'] as $item) {
                //если active=0, значит строка была скрыта/удалена, поэтому пропустим ее
                if (empty($item['active'])) continue;
                //если есть идентификатор отеля, то точно можно страну узнать
                if (!empty($item['hotelId'])) {
                    return true;
                }
            }
        }

        return false;
    }

    /*
     * Поиск по полю Параметры при Нестандартном запросе. Пытаемся найти вхождение Страны по списку всех стран
     * @param $postData - данные из формы
     */
    private static function findManagerInCustomFormByCountries($postData)
    {
        $countries = CountryDictionary::find()
            ->where(['active' => 1, 'trash' => 0])
            ->asArray()
            ->orderBy(['name' => SORT_ASC])
            ->all();
        if (!empty($postData['parametrs'])) {
            foreach ($countries as $country) {
                //если в тексте нашли вхождение страны
                if (preg_match('#'.$country['name'].'#siU', $postData['parametrs'])) {
                    return true;
                }
            }
        }

        return false;
    }

    /*
     * Поиск по полю Параметры при Нестандартном запросе. Пытаемся найти вхождение Страны и города
     * @param $postData - данные из формы
     * @param $conditions - массив услови менеджеров
     */
    private static function findManagerInCustomFormByCountryCity($postData, $conditions)
    {
        $onlyFields = ['country', 'city'];
        foreach ($conditions as $conditionManagerItem) {
            foreach ($conditionManagerItem as $conditionItem) {
                if (count($conditionItem) == 2 &&
                    isset($conditionItem['country']) &&
                    isset($conditionItem['city'])) {

                    if (!empty($postData['parametrs'])) {
                        //если в тексте нашли вхождение страны
                        if (preg_match('#'.$conditionItem['country'].'#siU', $postData['parametrs'])) {
                            //если в тексте нашли вхождение города
                            if (preg_match('#'.$conditionItem['city'].'#siU', $postData['parametrs'])) {
                                return $conditionManagerItem['manager_id'];
                            }
                        }
                    }
                }
            }
        }
        return false;
    }

    /*
     * Ищем менеджера в Турпакетах Комплексной формы
     */
    private static function findManagerInTourRows($postData, $conditions, $equalFields)
    {
        if (self::hasTours($postData)) {
            foreach ($postData['tour']['items'] as $item) {
                //если active=0, значит строка была скрыта/удалена, поэтому пропустим ее
                if (empty($item['active'])) continue;
                $countryName = $cityName = '';
                if (!empty($item['countryId'])) {
                    $countryName = static::findCountryNameById($item['countryId']);
                }
                if (!empty($item['cityId'])) {
                    $cityName = static::findCityNameById($item['cityId']);
                }
                //получаем Звездность отеля
                $stars = [];
                if (!empty($item['params']['tour_category'])) {
                    if (self::isStarsAny($item['params']['tour_category'])) {
                        $tmp[] = 'Любая';
                    } else {
                        $alloccat = AlloccatDictionary::find()
                            ->select('name')
                            ->where(['id' => $item['params']['tour_category'] ])
                            ->asArray()
                            ->all();
                        foreach ($alloccat as $alloccatItem) {
                            $stars[] = str_replace('*', '', $alloccatItem['name']);
                        }
                    }
                    $stars[] = 'Любая';
                }
                $currentData = [
                    'country' => $countryName,
                    'city' => $cityName,
                    'stars' => $stars,
                ];
                foreach ($conditions as $conditionManagerItem) {
                    $res = self::isEqualFields($equalFields, $conditionManagerItem['condition'], $currentData);
                    if ($res) {
                        return $conditionManagerItem['manager_id'];
                    }
                }
            }
        }

        return false;
    }

    /*
     * Ищем менеджера в Отелях Комплексной формы
     */
    private static function findManagerInHotelRows($postData, $conditions, $equalFields)
    {

        if (self::hasHotels($postData)) {
            $output[] = 'Данные по Конкретным отелям';
            $iter = 1;
            foreach ($postData['hotels']['items'] as $item) {
                //если active=0, значит строка была скрыта/удалена, поэтому пропустим ее
                if (empty($item['active'])) continue;
                if (!empty($item['hotelId'])) {
                    $hotel = HotelDictionary::find()
                        ->with('resort')
                        ->where(['id' => $item['hotelId']])
                        ->asArray()
                        ->one();
                    $cityName = $hotel['resort']['name'];
                    $countryName = static::findCountryNameById($hotel['resort']['country']);

                    //получаем Звездность отеля
                    $stars = [];
                    if (!empty($hotel['cat'])) {
                        $alloccat = AlloccatDictionary::find()
                            ->select('name')
                            ->where(['id' => $hotel['cat'] ])
                            ->asArray()
                            ->all();
                        foreach ($alloccat as $alloccatItem) {
                            $stars[] = str_replace('*', '', $alloccatItem['name']);
                        }
                    }
                    $currentData = [
                        'country' => $countryName,
                        'city' => $cityName,
                        'stars' => $stars,
                    ];
                    foreach ($conditions as $conditionManagerItem) {

                        $res = self::isEqualFields($equalFields, $conditionManagerItem['condition'], $currentData);
                        if ($res) {
                            return $conditionManagerItem['manager_id'];
                        }
                    }

                }
            }
        }

        return false;
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
        if (self::hasTours($postData)) {
            $iter = 1;
            foreach ($postData['tour']['items'] as $item) {
                //если active=0, значит строка была скрыта/удалена, поэтому пропустим ее
                if (empty($item['active'])) continue;
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
        if (self::hasHotels($postData)) {
            $iter = 1;
            foreach ($postData['hotels']['items'] as $item) {
                //если active=0, значит строка была скрыта/удалена, поэтому пропустим ее
                if (empty($item['active'])) continue;
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
                    $iter++;
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
        if (self::hasTours($postData)) {
            $iter = 1;
            foreach ($postData['tour']['items'] as $item) {
                //если active=0, значит строка была скрыта/удалена, поэтому пропустим ее
                if (empty($item['active'])) continue;
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
                    if (self::isStarsAny($item['params']['tour_category'])) {
                        $tmp[] = 'Любая';
                    } else {
                        $alloccat = AlloccatDictionary::find()
                            ->select('name')
                            ->where(['id' => $item['params']['tour_category'] ])
                            ->asArray()
                            ->all();
                        $tmp = [];
                        foreach ($alloccat as $alloccatItem) {
                            $tmp[] = $alloccatItem['name'];
                        }
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

                //Получаем Расположение. Разбираем передаваемые данные
                // Либо это в формате 3_2, где 3 - id категории, 2 - id удаленности
                // Либо там any - кога выбран любой тип.
                if (!empty($item['params']['tour_place'])) {
                    $tmp = [];
                    foreach ($item['params']['tour_place'] as $tourPlace) {
                        if ($tourPlace == 'any') {
                            $tourPlace = 'Любой тип';
                            $placeCategoryName = 'Любой тип';
                        } else {
                            list($tourPlaceCategory, $tourPlaceId) = explode('_' , $tourPlace);
                            $placeCategory = AllocPlaceTypeDictionary::find()
                                ->select('name')
                                ->where(['id' => $tourPlaceCategory])
                                ->asArray()
                                ->one();
                            $placeCategoryName = $placeCategory['name'];
                        }
                        if (!empty($tourPlaceId)) {
                            $tourPlace = AllocPlaceValueDictionary::find()
                                ->select('name')
                                ->where(['id' => $tourPlaceId, 'place' => $tourPlaceCategory])
                                ->asArray()
                                ->one();
                            $tmp[] = $tourPlace['name'];
                        }
                    }

                    $outputRow[] = 'Расположение ' . $placeCategoryName . ' ' . implode(',', $tmp);
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
        if (self::hasHotels($postData)) {
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
                if (empty($item['active'])) continue;
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

        //добавим данные заполненные клиентом в поле Дополнительные пожелания
        if (!empty($postData['params']['wish'])) {
            $output[] = $postData['params']['wish'];
        }

        return implode("\n", $output);
    }

    /*
     * Проверяет, что выбрана Любая звездность
     * @param $stars - массив с вариантами звездности
     */
    private static function isStarsAny($stars)
    {
        return (count($stars) == 1 && $stars[0] == 'any');
    }

    /*
     * Проверяет, есть ли в массиве данные по отелям (строки Конкретный отель)
     * * @param $postData - данные с формы
     */
    private static function hasTours($postData)
    {
        return (!empty($postData['params']['order_type']) &&
            $postData['params']['order_type'] == 'tours' &&
            array_key_exists('tour', $postData));
    }

    /*
     * Проверяет, есть ли в массиве данные по турам (строки Турпакета)
     * * @param $postData - данные с формы
     */
    private static function hasHotels($postData)
    {
        return (!empty($postData['params']['order_type']) &&
            $postData['params']['order_type'] == 'hotel' &&
            array_key_exists('hotels', $postData));
    }

}
