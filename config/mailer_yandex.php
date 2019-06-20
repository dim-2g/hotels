<?php

return [
    'class' => 'yii\swiftmailer\Mailer',
    'useFileTransport' => false,
    'transport' => [
        'class' => 'Swift_SmtpTransport',
        'host' => 'smtp.yandex.ru',
        'username' => 'hotels@modxguru.ru',
        'password' => 'Nl9kOz5E',
        'port' => '587',
        'encryption' => 'TLS',
    ],
];