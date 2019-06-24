<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\Booking;

class SiteController extends Controller
{
    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $formData = [];
        $dateNow = new \DateTime('+14day');
        $formData['dateFrom'] = $dateNow->format('Y-m-d');
        $dateNow->modify('+7day');
        $formData['dateTo'] = $dateNow->format('Y-m-d');
        $formData['nightFrom'] = 7;
        $formData['nightTo'] = 14;
        $formData['adults'] = 2;
        $formData['child'] = 0;

        return $this->render('index', [
            'data' => $formData
        ]);
    }

}