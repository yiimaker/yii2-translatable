[![Stand With Ukraine](https://raw.githubusercontent.com/vshymanskyy/StandWithUkraine/main/banner-direct.svg)](https://stand-with-ukraine.pp.ua)

<p align="center">
    <a href="https://github.com/yiimaker" target="_blank">
        <img src="https://avatars1.githubusercontent.com/u/24204902" height="100px">
    </a>
    <h1 align="center">Translatable behavior</h1>
</p>

[![Build Status](https://travis-ci.org/yiimaker/yii2-translatable.svg?branch=master)](https://travis-ci.org/yiimaker/yii2-translatable)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/yiimaker/yii2-translatable/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/yiimaker/yii2-translatable/?branch=master)
[![Total Downloads](https://img.shields.io/packagist/dt/yiimaker/yii2-translatable.svg)](https://packagist.org/packages/yiimaker/yii2-translatable)
[![Latest Stable Version](https://img.shields.io/packagist/v/yiimaker/yii2-translatable.svg)](https://packagist.org/packages/yiimaker/yii2-translatable)
[![StandWithUkraine](https://raw.githubusercontent.com/vshymanskyy/StandWithUkraine/main/badges/StandWithUkraine.svg)](https://github.com/vshymanskyy/StandWithUkraine/blob/main/docs/README.md)

Translatable behavior aggregates logic of linking translations to the primary model.

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
$ composer require yiimaker/yii2-translatable
```

or add

```
"yiimaker/yii2-translatable": "~1.0"
```

to the `require` section of your `composer.json`.

Usage
-----

1. Add behavior to the your primary model

```php
public function behaviors()
{
    return [
        // ...
        'translatable' => [
            'class' => TranslatableBehavior::className(),
            // 'translationRelationName' => 'translations',
            // 'translationLanguageAttrName' => 'language',
            // 'attributeNamePattern' => '%name% [%language%]',
            'translationAttributeList' => [
                'title',
                'description',
            ],
        ],
    ];
}
```

2. And use `getTranslation()` or `translateTo()` methods

```php
// product is an active record model with translatable behavior
$product = new Product();

// sets translation for default application language
$product->title = 'PhpStrom 2018.1';
$product->description = 'Лицензия PhpStrom IDE версия 2018.1';

// gets translation for English language
$translation = $product->getTranslation('en');
$translation->title = 'PhpStrom 2018.1';
$translation->description = 'License of the PhpStrom IDE version 2018.1';

// sets description for French language
$product->translateTo('fr')->description = 'La licence de PhpStorm IDE la version 2018.1';

$product->insert();
```

`translateTo()` it's just an alias for `getTranslation()` method.

After saving the model you can fetch this model from the database and translatable behavior will fetch all translations automatically.

```php
$product = Product::find()
    ->where(['id' => 1])
    ->with('translations')
    ->one()
;

// gets translation for English language
$product->translateTo('en')->description; // License of the PhpStrom IDE version 2018.1
// gets translation for French language
$product->translateTo('fr')->description; // La licence de PhpStorm IDE la version 2018.1

// check whether Ukrainian translation not exists
if (!$product->hasTranslation('uk')) {
    $product->translateTo('uk')->description = 'Ліцензія PhpStrom IDE версія 2018.1';
}

// update Enlish translation
$product->translateTo('en')->title = 'PhpStorm IDE';

$product->update();
```

Tests
-----

You can run tests with composer command

```
$ composer test
```

or using following command

```
$ codecept build && codecept run
```

Contributing
------------

For information about contributing please read [CONTRIBUTING.md](CONTRIBUTING.md).

Sponsoring
----------

<a href="https://www.buymeacoffee.com/OCmHjEY" target="_blank"><img src="https://cdn.buymeacoffee.com/buttons/v2/default-yellow.png" alt="Buy Me A Coffee" style="height: 60px !important;width: 217px !important;" ></a>

License
-------

[![License](https://img.shields.io/github/license/yiimaker/yii2-translatable.svg)](LICENSE)

This project is released under the terms of the BSD-3-Clause [license](LICENSE).

Copyright (c) 2017-2022, Yii Maker
