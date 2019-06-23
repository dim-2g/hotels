<?php

namespace app\models;

use Yii;
use \app\models\Dictionary\CountryDictionary;
use \app\models\Dictionary\CityDictionary;

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
    public $booking_directions = 'booking_directions';
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

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParams()
    {
        return $this->hasMany(Params::className(), ['entity' => $this->booking_directions, 'entity_id' => 'id']);
    }

    /**
     * Получение параметров отеля по ключу
     * @param $key - ключ для АР Params
     * @return mixed
     */
    public function getTourParams($key)
    {
        return $this->getParams()->andWhere(['category' => $key])->all();
    }

    /**
     * Питание в параметрах отеля
     * @return mixed
     */
    public function getMeals()
    {
        return $this->getTourParams('tour_meal');
    }

    /**
     * Категория отеля в параметрах отеля
     * @return mixed
     */
    public function getCategories()
    {
        return $this->getTourParams('tour_category');
    }

    /**
     * Получение параметров отеля по рейтингу
     * @return string
     */
    public function getRating()
    {
        return $this->getTourParams('tour_rating');
    }

    /**
     * Получение параметров расположения отеля
     * @return string
     */
    public function getPlace()
    {
        return $this->getTourParams('tour_place');
    }

    public function getForBaby()
    {
        return $this->getTourParams('tour_baby');
    }

    public function getOther()
    {
        return $this->getTourParams('tour_other');
    }

    public function getPlaceCategory()
    {
        $place = $this->getParams()->andWhere(['category' => 'tour_place'])->one();
        $placeCategory = explode('_', $place->value);
        $place->category = 'tour_place_category';
        $place->value = $placeCategory[0];
        return $place;
    }

    public function findValue($key)
    {
        switch ($key) {
            case 'country_id':
                return $this->country_id;
                break;
            case 'city_id':
                return $this->city_id;
                break;
            case 'alloccat_id':
                $output = [];
                if (!empty($this->categories)) {
                    foreach ($this->categories as $category) {
                        $output[] = $category->value;
                    }
                }
                return $output;
                break;
        }
    }
}
