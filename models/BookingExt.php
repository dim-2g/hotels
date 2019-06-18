<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;

/**
 * BookingForm is the model behind the contact form.
 */
class BookingExt extends Booking
{

    public function rules()
    {
        return [
            ['parametrs', 'trim', 'message' => 'Поле "Укажите страну, курорт или отель" обязательное для заполнения'],
            ['name', 'required', 'message' => 'Поле "Ваше имя" обязательное для заполнения'],
            ['phone', 'required', 'message' => 'Поле "Телефон" обязательное для заполнения'],
            ['email', 'email', 'message' => 'Поле не соответствует формату "ххххх@xxxx.xxxx"'],
            ['wish', 'trim'],
            ['date_departure', 'trim'],
            ['persons', 'trim'],
            ['budget', 'trim'],
            ['tourist_city', 'required', 'message' => 'Поле "Ваш город" обязательное для заполнения'],
            ['raw_data', 'trim'],
            ['manager_id', 'integer'],
            ['notified', 'integer'],
        ];
    }

}
