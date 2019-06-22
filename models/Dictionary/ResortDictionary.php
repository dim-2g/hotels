<?php

namespace app\models\Dictionary;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;
use app\models\Dictionary\AppDictionary;
use app\models\Dictionary\CountryDictionary;

class ResortDictionary extends AppDictionary
{

    public static function tableName()
    {
        return 'dict.dict_resort';
    }

    public function getCountryProfile()
    {
        return $this->hasOne(CountryDictionary::className(), ['id' => 'country']);
    }

}
