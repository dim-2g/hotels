<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;
use app\models\BookingHotels;
use app\models\BookingDirections;

/**
 * BookingForm is the model behind the contact form.
 */
class Booking extends ActiveRecord
{

    public static function tableName()
    {
        return 'booking';
    }

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            ['parametrs', 'trim', 'message' => 'Поле "Укажите страну, курорт или отель" обязательное для заполнения'],
            ['name', 'required', 'message' => 'Поле "Ваше имя" обязательное для заполнения'],
            ['phone', 'required', 'message' => 'Поле "Телефон" обязательное для заполнения'],
            ['email', 'email', 'message' => 'Поле не соответствует формату "ххххх@xxxx.xxxx"'],
            ['manager_id', 'integer'],
            ['notified', 'integer']
        ];
    }

    /*
     *
     */
    public function attributeLabels()
    {
        return [
            'name' => 'Ваше имя',
            'phone' => 'Ваш телефон',
            'email' => 'Ваш Email',
        ];
    }

    public function beforeSave($insert)
    {
        if ($insert) {
            $this->created_at = date("Y-m-d H:i:s");
        }
        return parent::beforeSave($insert);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHotels()
    {
        return $this->hasMany(BookingHotels::className(), ['booking_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDirections()
    {
        return $this->hasMany(BookingDirections::className(), ['booking_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getExtended()
    {
        return $this->hasOne(BookingExtended::className(), ['booking_id' => 'id']);
    }

}
