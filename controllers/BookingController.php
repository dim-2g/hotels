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

        $bookingForm = new BookingForm();
        if ($bookingForm->load(Yii::$app->request->post(), '')) {
            if ($bookingForm->validate()) {
                $resultSave = $bookingForm->save();
            } else {

                $errors = BookingHelper::prepareErrorsAjaxForm($bookingForm->getErrors());

                echo '<pre>';
                print_r($bookingForm->getErrors());
                print_r($errors);
                echo '</pre>';
            }
        }
        echo '<pre>';
        print_r($bookingForm);
        echo '</pre>';
        die('CustomRequest');
    }

}
