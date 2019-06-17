<?php

namespace app\controllers;

use app\helpers\BookingHelper;
use Yii;
use yii\web\Controller;
use app\models\CountryDictionary;
use app\models\CityDictionary;
use app\models\HotelDictionary;


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
            ->where(['name' => ['Москва', 'Санкт-Петербург', 'active' => 1, 'trash' => 0] ])
            ->orderBy(['name' => SORT_ASC])
            ->asArray()
            ->all();

        $citiesAll = CityDictionary::find()
            ->where(['name' => ['Алматы',
                                'Астана',
                                'Белгород',
                                'Брянск',
                                'Владикавказ',
                                'Волгоград',
                                'Воронеж',
                                'Гомель',
                                'Гродно',
                                'Екатеринбург',
                                'Иркутск',
                                'Калининград',
                                'Киев',
                                'Краснодар',
                                'Красноярск',
                                'Магадан',
                                'Махачкала',
                                'Минеральные воды',
                                'Мурманск',
                                'Набережные Челны',
                                'Нижний Новгород',
                                'Новосибирск',
                                'Омск',
                                'Оренбург',
                                'Пенза',
                                'Ростов-на-Дону',
                                'Саратов',
                                'Симферополь',
                                'Смоленск',
                                'Сочи',
                                'Томск',
                                'Ульяновск',
                                'Харьков',
                                'Челябинск',
                                'Шымкент',
                                'Якутск',
                                'Ярославль'],
                        'active' => 1,
                        'trash' => 0
            ])
            ->asArray()
            ->orderBy(['name' => SORT_ASC])
            ->all();

        $cities = array_merge($citiesFirst, $citiesAll);

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
