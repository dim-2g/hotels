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
use app\models\Params;
use app\modules\admin\models\Condition;
use app\modules\admin\models\Manager;
use Yii;
use yii\helpers\Url;
use yii\web\Controller;
use app\models\Booking;
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
        $post['type'] = 'custom';
        $booking = new Booking(['scenario' => Booking::SCENARIO_CUSTOM]);
        if ($booking->load($post, '')) {
            if ($booking->validate()) {
                $booking->save();
                //определим менеджера по заявке
                $booking->manager_id = self::findManagerToOrder($booking);
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
                $bookingExtended = new BookingExtended(['scenario' => $scenario]);
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
                    /*
                     * Запишем параметры выбранного направления
                     */
                    if (!empty($direction->id)) {
                        if (!empty($item['params'])){
                            foreach ($item['params'] as $paramCategory => $paramValues) {
                                foreach ($paramValues as $value) {
                                    $param = new Params();
                                    $param->entity = 'booking_directions';
                                    $param->entity_id = $direction->id;
                                    $param->category = $paramCategory;
                                    $param->value = $value;
                                    if ($param->validate()) {
                                        $param->save();
                                    }
                                }
                            }
                        }
                    }
                }
            }
            /*
             * Если форма содержит Отели, сохраняем их
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

                /*
                 * Запишем данные по питанию
                 */
                if (!empty($booking->id)) {
                    if (!empty($post['hotels']['meal'])) {
                        foreach ($post['hotels']['meal'] as $value) {
                            $param = new Params();
                            $param->entity = 'booking_params';
                            $param->entity_id = $booking->id;
                            $param->category = 'hotel_meal';
                            $param->value = $value;
                            if ($param->validate()) {
                                $param->save();
                            }
                        }
                    }
                }

            }

            //определим менеджера по заявке
            $booking->manager_id = self::findManagerToOrder($booking);
            $booking->save();
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
     * Второй шаг созранения комплексной формы.
     * Запоняем данными о пользователе
     * @return false|string
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
    }

    /**
     * Поиск сценария для валидации нужных полей
     * @param $postData - POST данные, в которых ищем значение order_type
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

    /**
     * Получение данных для заполнения направления
     * Ищем id страны, id города, id города вылета
     * @param $post - POST данные с формы
     * @return array
     */
    public static function findFieldsForDirections($post)
    {
        $orderDirections = [];
        $orderDirections['country_id'] = (!empty($post['countryId'])) ? $post['countryId'] : null;
        $orderDirections['city_id'] = (!empty($post['cityId'])) ? $post['cityId'] : null;
        $orderDirections['department_city_id'] = (!empty($post['departmentId'])) ? $post['departmentId'] : null;

        return $orderDirections;
    }

    /**
     * Получение данных для заполнения данных по отелям.
     * Ищем id отеля
     * @param $post - POST данные с формы
     * @return array
     */
    public static function findFieldsForHotels($post)
    {
        $orderHotels = [];
        $orderHotels['hotel_id'] = (!empty($post['hotelId'])) ? $post['hotelId'] : null;

        return $orderHotels;
    }

    /**
     * Получение данных для запонения расширенной формы
     * @param $post
     * @return array
     */
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
        $orderData['currency'] = self::findFieldByKeys($post, 'general', 'cur');

        return $orderData;
    }

    /**
     * Поиск необходдимых значений в POST данных
     *
     * @param  - POST данные с формы
     * @param $keyParentArray - ключ массива в котором искать значение. Данные могут
     * находится в :
     * - general - это массив из верхних контролов (дата вылета, кол-в ночей, кол-во гостей, цены)
     * - tours - данные по направлениям
     * - hotels - данные по конкретному отелю
     * - params - общие данные
     * @param $keyChildArray - ключ конкретного значения
     * @return string
     */
    public static function findFieldByKeys($postData, $keyParentArray, $keyChildArray)
    {
        if (isset($postData[$keyParentArray][$keyChildArray])) {
            return $postData[$keyParentArray][$keyChildArray];
        } else {
            return '';
        }
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

    /**
     * Поиск менеджера для заявки
     * @param $bookingId - идентификатор заявки
     */
    public static function findManagerToOrder($booking)
    {
        //проверим принадлежность классу
        if (!$booking instanceof Booking) {
            if (is_numeric($booking)) {
                $booking = Booking::find()->where(['id' => $booking])->one();
            }
        }
        //получаем все критерии для отбора менеджеров
        $conditions = Condition::findAllConditions();
        //проверяем в Турпакетах
        if ($booking->type == 'tours') {
            if ($managerId = self::findManagerTours($booking, $conditions)){
                return $managerId;
            }
        }
        //проверяем в Конкретном отеле
        if ($booking->type == 'hotels') {
            if ($managerId = self::findManagerHotels($booking, $conditions)){
                return $managerId;
            }
        }
        //для формы Нестандартный подбор
        if ($booking->type == 'custom') {
            //попробуем найти название Страны и Города
            if ($managerId = self::findManagerInCustomFormByCountryCity($booking, $conditions)){
                return $managerId;
            }
            //попробуем найти Вхождение страны и тогда назначим главного менеджера
            if ($managerId = self::findManagerInCustomFormByCountries($booking)){
                return $managerId;
            }
        }

        return null;
    }

    /**
     * Поиск менеджера по Туркакетам
     *
     * @param $booking - экземпляр Booking или id заявки
     * @param $conditions - условия для отбора менеджеров
     * @return bool or id менеджера
     */
    public static function findManagerTours($booking, $conditions)
    {
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
            $generalManager = Manager::findGeneralManager();
            if (!empty($generalManager->id)) {
                return $generalManager->id;
            }
        }

        return false;
    }

    /**
     * Поиск менеджера по Конкретному отелю
     *
     * @param $booking - экземпляр Booking или id заявки
     * @param $conditions - условия для отбора менеджеров
     * @return bool or id менеджера
     */
    public static function findManagerHotels($booking, $conditions)
    {
        $hasCountry = false;
        foreach ($booking->hotels as $hotel) {
            //проверяем каждый отель по всем критериям
            foreach ($conditions as $conditionItem) {
                $countEquals = 0;
                //проверяем по каждому условию
                foreach ($conditionItem['condition'] as $criterionKey => $criterionValue) {
                    $value = $hotel->findValue($criterionKey);
                    if (BookingHelper::isEqual($criterionValue, $value)) {
                        $countEquals++;
                    }
                }
                //если кол-во критериев и кол-во совпадений равно, то назначаем менеджера
                if (count($conditionItem['condition']) == $countEquals) {
                    return $conditionItem['manager_id'];
                }
            }
            if (!empty($hotel->findValue('country_id'))) {
                $hasCountry = true;
            }
        }
        //если не подошел ни один менеджер, то проверим наличие страны и назначим Главного
        if ($hasCountry) {
            $generalManager = Manager::findGeneralManager();
            if (!empty($generalManager->id)) {
                return $generalManager->id;
            }
        }

        return false;
    }

    /*
     * Поиск по полю Параметры при Нестандартном запросе. Пытаемся найти вхождение Страны по списку всех стран
     * @param $postData - данные из формы
     */
    private static function findManagerInCustomFormByCountries($booking)
    {
        $countries = CountryDictionary::findAllCountries();
        if (!empty($booking->parametrs)) {
            foreach ($countries as $country) {
                //если в тексте нашли вхождение страны
                if (preg_match('#'.$country['name'].'#siU', $booking->parametrs)) {
                    $generalManager = Manager::findGeneralManager();
                    if (!empty($generalManager->id)) {
                        return $generalManager->id;
                    }
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
    private static function findManagerInCustomFormByCountryCity($booking, $conditions)
    {
        $onlyFields = ['country_id', 'city_id'];
        foreach ($conditions as $conditionManagerItem) {
            foreach ($conditionManagerItem as $conditionItem) {
                //проверяем наличие 2х параметров Страна и Город в критериях
                if (count($conditionItem) == 2 &&
                    isset($conditionItem['country_id']) &&
                    isset($conditionItem['city_id'])) {

                    $countryName = CountryDictionary::findName($conditionItem['country_id']);
                    $cityName = CityDictionary::findName($conditionItem['city_id']);
                    if (!empty($booking->parametrs)) {
                        //если в тексте нашли вхождение страны
                        if (preg_match('#'.$countryName.'#siU', $booking->parametrs)) {
                            //если в тексте нашли вхождение города
                            if (preg_match('#'.$cityName.'#siU', $booking->parametrs)) {
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