<?php

namespace app\models;

use Yii;

class App extends \yii\db\ActiveRecord
{
    /**
     * Без перелета
     */
    const NO_FLY = '-1';

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

    /*
     *
     */
    public function attributeLabels()
    {
        return [
            'name' => 'Ваше имя',
            'phone' => 'Ваш телефон',
            'email' => 'Ваш Email',
            'parametrs' => 'Страна/Курорт/Отель',
            'created_at' => 'Дата добавл.',
            'wish' => 'Пожелания клиента',
            'date_departure' => 'Дата вылета',
            'persons' => 'Гости',
            'budget' => 'Бюджет',
            'tourist_city' => 'Город туриста',
            'manager_id' => 'Менеджер',
            'extended' => 'Доп.инфо',
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
            'department_city_id' => 'ID города вылета',
            'meal' => 'Питание',
            'currency' => 'Валюта',
        ];
    }
}
