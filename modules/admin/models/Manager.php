<?php

namespace app\modules\admin\models;

use Yii;

/**
 * This is the model class for table "manager".
 *
 * @property int $id
 * @property string $name
 * @property string $email
 */
class Manager extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'manager';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'email'], 'required'],
            [['name', 'email'], 'string'],
            [['active', 'general'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Имя',
            'email' => 'Email',
            'condition' => 'Условия',
            'active' => 'Работает',
            'general' => 'Главный',
        ];
    }
}
