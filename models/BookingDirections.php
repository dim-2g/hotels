<?php

namespace app\models;

use Yii;
use \app\models\Dictionary\CountryDictionary;
use \app\models\Dictionary\CityDictionary;
use \app\models\App;

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
class BookingDirections extends App
{
    /*
     * служит как ключ для идентификации значений в Params таблице 
    */
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
            'country_id' => 'Страна',
            'city_id' => 'Город',
            'department_city_id' => 'Город отправления',
            'params' => 'Параметры отеля',
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
     * Получить имя страны из связанных данных
     * @return |null
     */
    public function getCountryName()
    {
        if ($this->countryProfile) {
            return $this->countryProfile->name;
        }
        return null;
    }

    /**
     * Получение имени города из связанных данных
     * @return |null
     */
    public function getCityName()
    {
        if ($this->cityProfile) {
            return $this->cityProfile->name;
        }
        return null;
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
            if ($this->departmentCityProfile) {
                return $this->departmentCityProfile->name;
            }
        }

        return 'не указан';
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
     * Питание в параметрах отеля
     * @return mixed
     */
    public function getMealsString()
    {
        $output = [];
        foreach ($this->meals as $item) {
            $output[] = $item->valueText;
        }
        return implode(', ', $output);
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
     * Получение звездности отеля в виде строки
     * @return string
     */
    public function getStarsString()
    {
        $output = [];
        foreach ($this->categories as $item) {
            $output[] = $item->valueText;
        }
        asort($output);
        return implode(', ', $output);
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
     * Получение параметров отеля по рейтингу
     * @return string
     */
    public function getRatingString()
    {
        $output = [];
        foreach ($this->getTourParams('tour_rating') as $item) {
            $output[] = $item->valueText;
        }
        return implode(', ', $output);
    }

    /**
     * Получение параметров расположения отеля
     * @return string
     */
    public function getPlace()
    {
        return $this->getTourParams('tour_place');
    }

    /**
     * Получение параметров расположения отеля
     * @return string
     */
    public function getPlaceString()
    {
        $output = [];
        foreach ($this->getTourParams('tour_place') as $item) {
            $output[] = $item->valueText;
        }
        return implode(', ', $output);
    }

    /**
     * Получение выбранных значений "Детское" в параметрах отеля
     * @return mixed
     */ 
    public function getForBaby()
    {
        return $this->getTourParams('tour_baby');
    }

    /**
     * Получение выбранных значений "Детское" в параметрах отеля в виде строки
     * @return string
     */ 
    public function getForBabyString()
    {
        $output = [];
        foreach ($this->getTourParams('tour_baby') as $item) {
            $output[] = $item->valueText;
        }
        return implode(', ', $output);
    }

    /**
     * Получение Прочих параметров отеля
     * @return mixed
     */ 
    public function getOther()
    {
        return $this->getTourParams('tour_other');
    }

    /**
     * Получение Прочих параметров отеля, в виде строки
     * @return string
     */ 
    public function getOtherString()
    {
        $output = [];
        foreach ($this->getTourParams('tour_other') as $item) {
            $output[] = $item->valueText;
        }
        return implode(', ', $output);
    }

    /**
     * Получение id Месторасположения в параметрах отеля
     * @return mixed
     */ 
    public function getPlaceCategory()
    {
        $place = $this->getParams()->andWhere(['category' => 'tour_place'])->one();
        if ($place) {
            $placeCategory = explode('_', $place->value);
            $place->category = 'tour_place_category';
            $place->value = $placeCategory[0];
            return $place;
        }
        return null;
    }

    /**
     * Название группы Месторасположения в параметрах отеля
     * Пляжный, горный и т.д.
     * @return mixed
     */ 
    public function getPlaceCategoryName()
    {
        if ($this->placeCategory) {
            return $this->placeCategory->valueText;
        }
        return null;
    }

    /**
     * Получение свойства по ключу из Условий для соотнечения менеджеров и заявок
     * в условиях фигурируют такие ключи как сountry_id, city_id, alloccat_id
     * у объекта есть сountry_id, city_id, а alloccat_id вытаскивает из параметров
     * @return mixed
     */
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
