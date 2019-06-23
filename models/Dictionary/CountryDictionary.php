<?php

namespace app\models\Dictionary;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;
use app\models\Dictionary\AppDictionary;

class CountryDictionary extends AppDictionary
{

    public static function tableName()
    {
        return 'dict.dict_country';
    }

    /**
     * Находит название страны по id
     * @param $id
     * @return mixed
     */
    public static function findName($id)
    {
        if ($res = self::find()->where(['id' => $id])->one()){
            return $res->name;
        }

        return $id;
    }

}
