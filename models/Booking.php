<?php

namespace app\models;

use app\models\BookingDirections;
use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;
use app\models\BookingHotels;
use app\models\Params;


/**
 * BookingForm is the model behind the contact form.
 */
class Booking extends ActiveRecord
{
    /**
     * Сценарий правил валидации для Нестандартного запроса
     */
    const SCENARIO_CUSTOM = 'custom';

    /**
     * Сценарий правил валидации на первом шаге сложной формы
     */
    const SCENARIO_FIRST_STEP = 'first_step';

    /**
     * Сценарий правил валидации для Турпакета
     */
    const SCENARIO_TOURS = 'tours';

    /**
     * Сценарий правил валидации для Конкретного отеля
     */
    const SCENARIO_HOTELS = 'hotels';

    public $booking_params = 'booking_params';

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
            ['type', 'trim'],
            ['tourist_city_id', 'required', 'message' => 'Поле "Ваш город" обязательное для заполнения'],
            ['notified', 'integer']

        ];
    }

    /**
     * Сценарии
     * @return array
     */

    public function scenarios()
    {
        $scenarios = parent::scenarios();

        $scenarios[static::SCENARIO_CUSTOM] = ['parametrs', 'name', 'phone', 'email', 'type'];
        $scenarios[static::SCENARIO_FIRST_STEP] = ['type'];
        $scenarios[static::SCENARIO_TOURS] = ['parametrs', 'name', 'phone', 'email', 'type', 'tourist_city_id'];
        $scenarios[static::SCENARIO_HOTELS] = ['parametrs', 'name', 'phone', 'email', 'type', 'tourist_city_id'];


        return $scenarios;
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

    public function beforeDelete()
    {
        if (parent::beforeDelete()) {
            $this->unlinkAll('extended', true);
            $this->unlinkAll('params', true);
            return true;
        } else {
            return false;
        }
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
    public function getParams()
    {
        return $this->hasMany(Params::className(), ['entity' => $this->booking_params, 'entity_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getExtended()
    {
        return $this->hasOne(BookingExtended::className(), ['booking_id' => 'id']);
    }

}
