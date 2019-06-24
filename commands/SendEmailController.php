<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use app\controllers\BookingController;
use app\models\Booking;
use app\modules\admin\models\Manager;
use yii\console\Controller;
use yii\console\ExitCode;


/*
 * Отправляем уведомления о заказе
 * Сравнивает текущее время и время создания заявки
 * если прошло более 2х минут - отправляем уведомление, меняем поле notified на 1
 */
class SendEmailController extends Controller
{
    public function actionIndex()
    {
        $order = Booking::findOne(313);
        print_r($order);
        $order->notified = 1;
        print_r($order);
        $order->save();
        print_r($order);
        die('OK');

        $orders = Booking::find()->where(['notified' => null])
                                 ->andWhere(['not', ['manager_id' => null]])
                                 ->all();

        foreach ($orders as $order) {
            if (self::isTimeHasCome($order->created_at)) {
                if (BookingController::sendMailEx($order)) {
                    $order->notified = 1;
                    $order->save();
                }
            }
        }

        return ExitCode::OK;
    }

    private function isTimeHasCome($orderTime)
    {
        return true;
        $orderTime = \DateTime::createFromFormat('Y-m-d H:i:s', $orderTime)->getTimestamp();
        $timeDiff = time() - $orderTime;
        if ($timeDiff > 2*60) {
            return true;
        }
        return false;
    }
}
