<?php

namespace app\helpers;


use Yii;
use yii\helpers\Url;
use app\models\CountryDictionary;
use app\models\CityDictionary;
use app\models\HotelDictionary;
use app\models\AlloccatDictionary;
use app\models\AllocPlaceTypeDictionary;
use app\models\AllocPlaceValueDictionary;

class BookingHelper{

    public static $flagsDirectory = '/images/flags/';

    /*
     * Проводит трансформацию массива ошибок для ajax ответа
     */
    public static function prepareErrorsAjaxForm($errors)
    {
        $output = [];
        foreach ($errors as $key => $texts) {
            if (!empty($texts[0])) {
                $output[] = [
                    'key' => $key,
                    'text' => $texts[0]
                ];
            }
        }

        return $output;
    }

    /*
     * Добавляем каждой стране поле со строкой до изображения флага
     * $countries - массив с данными по странам
     * $fieldNameEng - название поля с латинским написанием страны
     */
    public static function prepareCountryFlags($countries, $fieldNameEng = 'name_eng')
    {
        foreach ($countries as &$country) {
            $flagFileName = static::findCountryFlagName($country[$fieldNameEng]);
            $fullFlagFileName = $_SERVER['DOCUMENT_ROOT']  . static::$flagsDirectory . $flagFileName;
            if (is_readable($fullFlagFileName)) {
                $country['flag_image'] = static::$flagsDirectory . $flagFileName;
            }
        }
        return $countries;
    }

    /*
     * Преобразуем англ. название страны к названию изображения флага
     * $countryName - название страны на англ.языке
     */
    private static function findCountryFlagName($countryName)
    {
        $flagFileName = trim(strtolower($countryName));
        $flagFileName = str_replace('  ', ' ', $flagFileName);
        $flagFileName = str_replace(' ', '_', $flagFileName);
        $flagFileName .= '.jpg';
        return $flagFileName;
    }

    /**
     * Проверяет на соответствие два значения
     * Если второе массив, то ищет хотя бы одно совпадение
     * @return string
     */
    public static function isEqual($conditionValue, $targetValue)
    {
        if (is_array($targetValue)) {
            return in_array($conditionValue, $targetValue);
        } else {
            return $conditionValue == $targetValue;
        }
    }

    /**
     * Возвращает отформатированную строку для цен
     */
    public static function priceFormat($price)
    {
        return number_format($price, 0, '', ' ' );
    }

}