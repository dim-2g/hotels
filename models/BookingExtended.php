<?php

namespace app\models;

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
class BookingExtended extends \yii\db\ActiveRecord
{
    /**
     * Сценарий правил для Нестандартного запроса
     */
    const SCENARIO_CUSTOM = 'custom';

    /**
     * Сценарий правил для Турпакета
     */
    const SCENARIO_TOURS = 'tours';

    /**
     * Сценарий правил для Конкретного отеля
     */
    const SCENARIO_HOTELS = 'hotels';

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
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'booking_id' => 'Booking ID',
            'date_from' => 'Дата вылета от',
            'date_to' => 'Дата вылета до',
            'night_from' => 'Ночей от',
            'night_to' => 'Ночей до',
            'adult' => 'Кол-во взрослых',
            'child' => 'Кол-во детей',
            'child_age_1' => 'Возраст 1 ребенка',
            'child_age_2' => 'Возраст 2 ребенка',
            'child_age_3' => 'Возраст 3 ребенка',
            'price_comfort' => 'Бюджет комфортный',
            'price_max' => 'Бюджет максимальный',
            'wish' => 'Пожелания',
            'department_city_id' => 'ID города вылета',
            'meal' => 'Питание',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBooking()
    {
        return $this->hasOne(Booking::className(), ['id' => 'booking_id']);
    }
}
