<?php

namespace app\helpers;

use Yii;

class BookingHelper{

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

        return Yii::$app->mailer->compose()
            ->setFrom('hotels@modxguru.ru')
            ->setTo($emailTo)
            ->setSubject('Добавлена новая заявка')
            ->setHtmlBody(implode("<br />", $message))
            ->send();
    }

}