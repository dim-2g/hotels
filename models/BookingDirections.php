<?php

namespace app\models;

use Yii;
use \app\models\CountryDictionary;
use \app\models\CityDictionary;

/**
 * This is the model class for table "booking_directions".
 *
 * @property int $id
 * @property int $booking_id
 * @property int $country_id
 * @property int $city_id
 * @property int $department_city_id
 * @property int $params
 *
 * @property Booking $booking
 */
class BookingDirections extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'booking_directions';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['booking_id'], 'required'],
            [['booking_id', 'country_id', 'city_id', 'department_city_id'], 'integer'],
            [['params'], 'safe'],
            [['booking_id'], 'exist', 'skipOnError' => true, 'targetClass' => Booking::className(), 'targetAttribute' => ['booking_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'booking_id' => 'Booking ID',
            'country_id' => 'Country ID',
            'city_id' => 'City ID',
            'department_city_id' => 'Department City ID',
            'params' => 'Params',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBooking()
    {
        return $this->hasOne(Booking::className(), ['id' => 'booking_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCountryProfile()
    {
        return $this->hasOne(CountryDictionary::className(), ['id' => 'country_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCityProfile()
    {
        return $this->hasOne(CityDictionary::className(), ['id' => 'city_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDepartmentCityProfile()
    {
        return $this->hasOne(CityDictionary::className(), ['id' => 'department_city_id']);
    }
}
