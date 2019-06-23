<?php

namespace app\models;

use app\controllers\BookingController;
use app\models\Dictionary\AlloccatDictionary;
use app\models\Dictionary\AllocPlaceTypeDictionary;
use app\models\Dictionary\AllocPlaceValueDictionary;
use Yii;

/**
 * This is the model class for table "params".
 *
 * @property int $id
 * @property string $entity
 * @property int $entity_id
 * @property string $category
 * @property string $value
 */
class Params extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'params';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['entity', 'entity_id', 'category', 'value'], 'required'],
            [['entity', 'category', 'value'], 'string'],
            [['entity_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'entity' => 'Entity',
            'entity_id' => 'Entity ID',
            'category' => 'Category',
            'value' => 'Value',
        ];
    }

    /**
     * {@inheritdoc}
     * @return ParamsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ParamsQuery(get_called_class());
    }

    /**
     * Получение текстового значения параметра
     * @return string
     */
    public function getValueText()
    {
        $valueText = $this->value;
        switch ($this->category) {
            case 'tour_meal':
                if ($this->value == 'any') {
                    $valueText = 'Любое';
                }
                break;

            case 'tour_category':
                if ($this->value == 'any') {
                    $valueText = 'Любой тип';
                } else {
                    $category = AlloccatDictionary::find()->select('name')->where(['id' => $this->value])->one();
                    $category['name'] = str_replace('*', '', $category['name']);
                    $valueText = $category['name'];
                }
                break;

            case 'tour_place_category':
                if ($this->value == 'any') {
                    $valueText = 'Любой тип';
                } else {
                    $placeCategory = AllocPlaceTypeDictionary::find()
                        ->select('name')
                        ->where(['id' => $this->value])
                        ->asArray()
                        ->one();
                    $valueText = $placeCategory['name'];
                }
                break;

            case 'tour_place':
                if ($this->value == 'any') {
                    $valueText = 'Любой тип';
                } else {
                    list($tourPlaceCategory, $tourPlaceId) = explode('_' , $this->value);
                    if (!empty($tourPlaceId)) {
                        $tourPlace = AllocPlaceValueDictionary::find()
                            ->select('name')
                            ->where(['id' => $tourPlaceId, 'place' => $tourPlaceCategory])
                            ->asArray()
                            ->one();
                        $valueText = $tourPlace['name'];
                    }
                }
                break;

            case 'tour_rating':
                if ($this->value == 'not_important') {
                    $valueText = 'не важен';
                } else {
                    if (!empty($this->value)) {
                        $valueText = 'не менее ' . $this->value;
                    } else {
                        $valueText = 'не важен';
                    }

                }
                break;

            case 'tour_baby':
                if (array_key_exists($this->value, BookingController::$childrenParams)) {
                    $valueText = BookingController::$childrenParams[ $this->value ];
                }
                break;

            case 'tour_other':
                if (array_key_exists($this->value, BookingController::$otherParams)) {
                    $valueText = BookingController::$otherParams[ $this->value ];
                }
                break;
            default:
                return $this->value;
        }

        return $valueText;
    }
}
