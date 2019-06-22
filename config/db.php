<?php

return [
    'class' => 'yii\db\Connection',
    'dsn' => 'sqlite:'.dirname(__DIR__) . '/db/sqlite.db',
    'enableSchemaCache' => true,
    'charset' => 'utf8',
];