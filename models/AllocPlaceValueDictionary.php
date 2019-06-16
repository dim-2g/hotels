<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;
use app\models\AppDictionary;
use app\models\AllocPlaceTypeDictionary;

class AllocPlaceValueDictionary extends AppDictionary
{

    public static function tableName()
    {
        return 'dict.dict_alloc_place_value';
    }

    /*
    public function getType()
    {
        return $this->hasOne(AllocPlaceTypeDictionary::className(), ['id' => 'place']);
    }*/

}
