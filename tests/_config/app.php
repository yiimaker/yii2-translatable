<?php

return [
    'id' => 'test-app',
    'class' => yii\console\Application::className(),
    'language' => 'en',

    'basePath' => Yii::getAlias('@tests'),
    'vendorPath' => Yii::getAlias('@vendor'),
    'runtimePath' => Yii::getAlias('@tests/_output'),

    'bootstrap' => [],
    'components' => [
        'db' => require __DIR__ . '/db.php',
    ],
    'params' => [],
];
