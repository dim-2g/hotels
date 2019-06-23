<?php

namespace app\models;

use Yii;
use app\models\Dictionary\HotelDictionary;

/**
 * This is the model class for table "booking_hotels".
 *
 * @property int $id
 * @property int $booking_id
 * @property int $hotel_id
 *
 * @property Booking $booking
 */
class BookingHotels extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'booking_hotels';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['booking_id', 'hotel_id'], 'required'],
            [['booking_id', 'hotel_id'], 'integer'],
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
            'hotel_id' => 'Hotel ID',
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
    public function getHotelProfile()
    {
        return $this->hasOne(HotelDictionary::className(), ['id' => 'hotel_id']);
    }

    /**
     * Получение звездности отеля
     * @return mixed
     */
    public function getStars()
    {
        return $this->hotelProfile->categoryProfile->value;
    }

    /**
     * Получение имени города, в котором расположен отель
     * @return mixed
     */
    public function getCityName()
    {
        return $this->hotelProfile->resortProfile->name;
    }

    /**
     * Получение страны, в которой расположен отель
     * @return mixed
     */
    public function getCountryName()
    {
        return $this->hotelProfile->resortProfile->countryProfile->name;
    }

    /**
     * Получение названия отеля
     * @return mixed
     */
    public function getName()
    {
        return $this->hotelProfile->name;
    }

    public function findValue($key)
    {
        switch ($key) {
            case 'country_id':
                return $this->hotelProfile->resortProfile->countryProfile->id;
                break;
            case 'city_id':
                return $this->hotelProfile->resortProfile->id;
                break;
            case 'alloccat_id':
                return $this->hotelProfile->categoryProfile->id;
                break;
        }
    }

}
