<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;
use app\models\AppDictionary;
use app\models\CountryDictionary;

class ResortDictionary extends AppDictionary
{

    public static function tableName()
    {
        return 'dict.dict_resort';
    }

    public function getCountry()
    {
        return $this->hasOne(CountryDictionary::className(), ['id' => 'country']);
    }

}
