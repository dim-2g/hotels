<?php

namespace app\modules\admin\models;

use Yii;

/**
 * This is the model class for table "condition".
 *
 * @property int $id
 * @property int $manager_id
  * @property string $condition
 *
 * @property Manager $manager
 */
class Condition extends \yii\db\ActiveRecord
{
    public static $fieldName = [
        'country' => 'Страна',
        'city' => 'Курорт',
        'stars' => 'Звездность',
    ];
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'condition';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {

        return [
            [['manager_id', 'condition'], 'required'],
            [['manager_id'], 'integer'],
            [['condition'], 'string'],
            [['manager_id'], 'exist', 'skipOnError' => true, 'targetClass' => Manager::className(), 'targetAttribute' => ['manager_id' => 'id']],
        ];

    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {

        return [
            'id' => 'ID',
            'manager_id' => 'Manager ID',
            'condition' => 'Условия в формате JSON',

        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getManager()
    {
        return $this->hasOne(Manager::className(), ['id' => 'manager_id']);
    }

    public static function findAllConditions()
    {
        $output = [];
        $conditions = self::find()->all();
        foreach ($conditions as $item) {
            if (!empty($item->condition)) {
                $condition = json_decode($item->condition, true);
            }
            $output[] = [
                'manager_id' => $item['manager_id'],
                'condition' => $condition
            ];
        }
        return $output;
    }
}
