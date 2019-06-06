<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\Booking;
use app\helpers\BookingHelper;

class BookingController extends Controller
{


    public function beforeAction($action)
    {
        if ($action->id == 'custom') {
            $this->enableCsrfValidation = false;
        }
        return parent::beforeAction($action);
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    /*
     * Обрабатываем поступающий ajax запрос
     * Возвращаем json
     * success = true в случае успеха
     * success = false если есть ошибки
     * errors - массив ошибок (поле => описание ошибки)
     */
    public function actionCustom()
    {
        $response = [
            'success' => false,
            'errors' => [],
        ];

        $booking = new Booking();
        if ($booking->load(Yii::$app->request->post(), '')) {
            if ($booking->validate()) {
                $booking->save();
                BookingHelper::sendMail($booking, 'dim-2g@yandex.ru');
                $response['success'] = true;
            } else {
                $response['errors'] = BookingHelper::prepareErrorsAjaxForm($booking->getErrors());
            }
        }
        return json_encode($response);
    }

}
