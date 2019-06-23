<?php

namespace app\models\Dictionary;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;
use app\models\Dictionary\AppDictionary;

class AlloccatDictionary extends AppDictionary
{

    public static function tableName()
    {
        return 'dict.dict_alloccat';
    }

    public function getValue()
    {
        $value = trim(str_replace('*', '', $this->name));
        return $value;
    }

    public static function findName($id)
    {
        if ($res = self::find()->where(['id' => $id])->one()){
            return $res->name;
        }

        return $id;
    }

}
