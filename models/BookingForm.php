<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;

/**
 * BookingForm is the model behind the contact form.
 */
class BookingForm extends ActiveRecord
{
    /*
    public $message;
    public $name;
    public $email;
    public $phone;
*/
    public static function tableName()
    {
        return 'booking';
    }

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['name', 'message', 'phone'], 'required'],
            ['email', 'email'],
            ['name', 'safe'],
            ['message', 'safe'],
            ['phone', 'safe'],
        ];
    }

}
