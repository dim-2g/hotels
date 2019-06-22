<?php

namespace app\models\Dictionary;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;
use app\models\Dictionary\AppDictionary;
use app\models\Dictionary\ResortDictionary;
use app\models\Dictionary\AlloccatDictionary;
use PDO;

class HotelDictionary extends AppDictionary
{

    public static function tableName()
    {
        return 'dict.dict_allocation';
    }

    public function getResortProfile()
    {
        return $this->hasOne(ResortDictionary::className(), ['id' => 'resort']);
    }

    public function getCategoryProfile()
    {
        return $this->hasOne(AlloccatDictionary::className(), ['id' => 'cat']);
    }

    public static function findHotels($query)
    {
        $queryHotels = Yii::$app->dbDictionary->createCommand("
            SELECT 
                da.id, da.name, da.resort, 
                dr.country, dr.name as resort_name,
                dc.name as country_name, dc.name_eng as country_name_eng,
                dac.name as hotel_category
            FROM 
                dict.dict_allocation da
            LEFT JOIN
                dict.dict_resort dr
            ON 
                (da.resort = dr.id)
            LEFT JOIN
                dict.dict_country dc
            ON 
                (dr.country = dc.id)
            LEFT JOIN
                dict.dict_alloccat dac
            ON 
                (da.cat = dac.id)    
            WHERE 
                da.name ILIKE :query 
            LIMIT 10
        ")->bindValue(':query', '%'.$query.'%', PDO::PARAM_STR)
          ->queryAll();

        return $queryHotels;
    }

}
