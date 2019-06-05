<?php

namespace app\helpers;

class BookingHelper{

    /*
     * Проводит трансформацию массива ошибок для ajax ответа
     */
    public static function prepareErrorsAjaxForm($errors)
    {
        $output = [];
        foreach ($errors as $key => $texts) {
            if (!empty($texts[0])) {
                $output[$key] = $texts[0];
            }
        }

        return $output;
    }

}