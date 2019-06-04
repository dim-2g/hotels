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

        $bookingForm = new BookingForm();
        $bookingForm->name = 'Ivan';
        $bookingForm->email = 'ivan@test.ru';
        $bookingForm->phone = '7777';
        $bookingForm->message = 'Проверка';
        $bookingForm->save(false);
        echo '<pre>';
        print_r(Yii::$app->request->post());
        echo '</pre>';

        if ($bookingForm->load(Yii::$app->request->post(), '')) {
            if ($bookingForm->validate()) {
                $bookingForm->save();
            } else {
                echo '<pre>';
                print_r($bookingForm->getErrors());
                echo '</pre>';
            }
        }
        echo '<pre>';
        print_r($bookingForm);
        echo '</pre>';
        die('CustomRequest');
    }

}
