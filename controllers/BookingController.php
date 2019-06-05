<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\BookingForm;
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

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionCustom()
    {
        $response = [
            'success' => false,
            'errors' => [],
        ];

        $bookingForm = new BookingForm();
        if ($bookingForm->load(Yii::$app->request->post(), '')) {
            if ($bookingForm->validate()) {
                $bookingForm->save();
                BookingHelper::sendMail($bookingForm, 'dim-2g@yandex.ru');
                $response['success'] = true;
            } else {
                $response['errors'] = BookingHelper::prepareErrorsAjaxForm($bookingForm->getErrors());
            }
        }
        return json_encode($response);
    }

}
