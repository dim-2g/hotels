<?php

return [
    'class' => 'yii\swiftmailer\Mailer',
    'useFileTransport' => false,
    'transport' => [
        'class' => 'Swift_SmtpTransport',
        'host' => 'smtp.gmail.com',
        'username' => 'test.th.welcome@gmail.com',
        'password' => 'xsfnqiubaakiaswt',
        'port' => '465',
        'encryption' => 'ssl',
    ],
];