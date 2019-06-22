<?php

namespace app\models\Dictionary;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;

class AppDictionary extends ActiveRecord
{

    public static function getDb()
    {
        return Yii::$app->dbDictionary;
    }

}
