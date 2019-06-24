<?php

namespace app\models;

use app\controllers\BookingController;
use app\models\App;
use app\models\Dictionary\CityDictionary;
use Yii;

/**
 * This is the model class for table "booking_ext".
 *
 * @property int $id
 * @property int $booking_id
 * @property string $date_from
 * @property string $date_to
 * @property string $night_from
 * @property string $night_to
 * @property int $adult
 * @property int $child
 * @property int $child_age_1
 * @property int $child_age_2
 * @property int $child_age_3
 * @property int $price_comfort
 * @property int $price_max
 * @property string $wish
 * @property int $tourist_city_id
 * @property int $meal
 *
 * @property Booking $booking
 */
class BookingExtended extends App
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'booking_ext';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['booking_id', 'date_from', 'date_to', 'night_from', 'night_to', 'adult', 'tourist_city_id'], 'required'],
            [['booking_id', 'adult', 'child', 'child_age_1', 'child_age_2', 'child_age_3', 'price_comfort', 'price_max', 'currency', 'department_city_id'], 'integer'],
            [['date_from', 'date_to', 'night_from', 'night_to'], 'string'],
            [['wish'], 'trim'],
            [['booking_id'], 'exist', 'skipOnError' => true, 'targetClass' => Booking::className(), 'targetAttribute' => ['booking_id' => 'id']],
        ];
    }

    /**
     * Сценарии
     * @return array
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[static::SCENARIO_TOURS] = [
            'booking_id',
            'date_from',
            'date_to',
            'night_from',
            'night_to',
            'adult',
            'child',
            'child_age_1',
            'child_age_2',
            'child_age_3',
            'price_comfort',
            'price_max',
            'currency',
            'wish',
        ];
        $scenarios[static::SCENARIO_HOTELS] = [
            'booking_id',
            'date_from',
            'date_to',
            'night_from',
            'night_to',
            'adult',
            'child',
            'child_age_1',
            'child_age_2',
            'child_age_3',
            'price_comfort',
            'price_max',
            'currency',
            'wish',
            'department_city_id',
        ];

        return $scenarios;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBooking()
    {
        return $this->hasOne(Booking::className(), ['id' => 'booking_id']);
    }

    /**
     * Получение валюты в читаемом виде
     * @return |null
     */
    public function getCurrencyString()
    {
        if (array_key_exists($this->currency, BookingController::$currency)) {
            return BookingController::$currency[$this->currency];
        }
        return $this->currency;
    }

    /**
     * Получение имени города отправления из связанных данных
     * @return |null
     */
    public function getDepartmentCityName()
    {
        if ($this->department_city_id == App::NO_FLY) {
            return 'без перелета';
        } else {
            return CityDictionary::findName($this->department_city_id);
        }
    }

}
