<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use app\controllers\BookingController;
use app\modules\admin\models\Booking;
use app\modules\admin\models\Manager;
use yii\console\Controller;
use yii\console\ExitCode;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class SendEmailController extends Controller
{
    public function actionIndex()
    {

        $orders = Booking::find()->where(['notified' => null])
                                 ->andWhere(['not', ['manager_id' => null]])
                                 ->all();

        foreach ($orders as $order) {
            if (self::isTimeHasCome($order->created_at)) {
                $manager = Manager::find()->where(['id' => $order->manager_id])->one();
                if (BookingController::sendMailEx($order, $manager)) {
                    $order->notified = 1;
                    $order->save();
                }
            }
        }

        return ExitCode::OK;
    }

    private function isTimeHasCome($orderTime)
    {
        $orderTime = \DateTime::createFromFormat('Y-m-d H:i:s', $orderTime)->getTimestamp();
        $timeDiff = time() - $orderTime;
        if ($timeDiff > 2*60) {
            return true;
        }
        return false;
    }
}
