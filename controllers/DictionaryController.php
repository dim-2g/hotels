<?php

namespace app\controllers;

use app\helpers\BookingHelper;
use Yii;
use yii\web\Controller;
use app\models\Dictionary\CountryDictionary;
use app\models\Dictionary\CityDictionary;
use app\models\Dictionary\HotelDictionary;

class DictionaryController extends Controller
{

    public function actionIndex()
    {
        return $this->render('index');
    }

    /*
     * Получаем список всех стран
     */
    public function actionCountries()
    {
        $countries = CountryDictionary::find()
            ->where(['active' => 1, 'trash' => 0])
            ->asArray()
            ->orderBy(['name' => SORT_ASC])
            ->all();
        $countries = BookingHelper::prepareCountryFlags($countries);

        return json_encode($countries);
    }

    /*
     * Получаем список всех городов для указанной страны
     * $countryId - id страны
     */
    public function actionCities($countryId)
    {
        $cities = CityDictionary::find()
            ->where(['country' => $countryId, 'active' => 1, 'trash' => 0])
            ->asArray()
            ->orderBy(['name' => SORT_ASC])
            ->all();

        return json_encode($cities);
    }

    public function actionDepartment()
    {
        $citiesFirst = CityDictionary::find()
            ->where(['id' => [
                                '212', //Москва
                                '294', //Санкт-Петербург
                             ],
                     'active' => 1,
                     'trash' => 0
            ])
            ->orderBy(['name' => SORT_ASC])
            ->asArray()
            ->all();

        $citiesAll = CityDictionary::find()
            ->where(['id' => [
                                '6850', //Алматы
                                '6849', //Астана
                                '34', //Белгород
                                '57', //Брянск
                                '78', //Владикавказ
                                '80', //Волгоград
                                '84', //Воронеж
                                '420', //Гомель
                                '421', //Гродно
                                '109', //Екатеринбург
                                '132', //Иркутск
                                '141', //Калининград
                                '429', //Киев
                                '175', //Краснодар
                                '176', //Красноярск
                                '195', //Магадан
                                '199', //Махачкала
                                '204', //Минеральныйе воды
                                '215', //Мурманск
                                '218', //Набережные Челны
                                '217', //Нижний Новгород
                                '236', //Новосибирск
                                '249', //Омск
                                '251', //Оренбург
                                '260', //Пенза
                                '286', //Ростов-на-Дону
                                '296', //Саратов
                                '314', //Симферополь
                                '318', //Смоленск
                                '323', //Сочи
                                '354', //Томск
                                '367', //Ульяновск
                                '382', //Харьков
                                '388', //Челябинск
                                '7253', //Шымкент
                                '415', //Якутск
                                '417', //Ярославль
                                ],
                        'active' => 1,
                        'trash' => 0
            ])
            ->asArray()
            ->orderBy(['name' => SORT_ASC])
            ->all();

        $withoutFly = new \stdClass();
        $withoutFly->id = '';
        $withoutFly->name = 'без перелета';

        $cities = array_merge(
            [$withoutFly],
            $citiesFirst,
            $citiesAll
        );

        return json_encode($cities);
    }

    /*
     * Получаем список отелей
     * $query - часть поисковой фразы
     */
    public function actionHotels($query)
    {
        $hotels = HotelDictionary::findHotels($query);
        $hotels = BookingHelper::prepareCountryFlags($hotels, 'country_name_eng');
        return json_encode($hotels);
    }

    /*
     * Получаем список отелей
     * $query - часть поисковой фразы
     */
    public function actionCityTourist($query)
    {
        $cities = CityDictionary::find()
            ->where(['ilike', 'name', $query])
            ->andWhere(['active' => 1, 'trash' => 0])
            ->orderBy(['name' => SORT_ASC])
            ->asArray()
            ->all();

        return json_encode($cities);
    }


}
