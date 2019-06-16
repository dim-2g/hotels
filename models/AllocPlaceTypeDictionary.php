<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;
use app\models\AppDictionary;

class AllocPlaceTypeDictionary extends AppDictionary
{

    public static function tableName()
    {
        return 'dict.dict_alloc_place_type';
    }

}
