<?php

namespace app\modules\admin\models;

use Yii;

/**
 * This is the model class for table "booking".
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $phone
 * @property string $parametrs
 * @property string $created_at
 */
class Booking extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'booking';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'email', 'phone', 'parametrs', 'created_at'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => '№ заявки',
            'name' => 'Имя',
            'email' => 'E-mail',
            'phone' => 'Телефон',
            'parametrs' => 'Страна/Курорт/Отель',
            'created_at' => 'Дата создания',
        ];
    }
}
