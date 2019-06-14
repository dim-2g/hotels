<?php

namespace app\helpers;

use Yii;
use yii\helpers\Url;

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

    public static function prepareCountryFlags($countries)
    {
        foreach ($countries as &$country) {
            $flagFileName = static::findCountryFlagName($country['name_eng']);
            $fullFlagFileName = $_SERVER['DOCUMENT_ROOT']  . static::$flagsDirectory . $flagFileName;
            if (is_readable($fullFlagFileName)) {
                $country['flag_image'] = static::$flagsDirectory . $flagFileName;
            }
        }
        return $countries;
    }

    private static function findCountryFlagName($countryName) {
        $flagFileName = trim(strtolower($countryName));
        $flagFileName = str_replace('  ', ' ', $flagFileName);
        $flagFileName = str_replace(' ', '_', $flagFileName);
        $flagFileName .= '.jpg';
        return $flagFileName;
    }
}