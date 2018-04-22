<?php
/**
 * @link https://github.com/yiimaker/yii2-translatable
 * @copyright Copyright (c) 2017-2018 Yii Maker
 * @license BSD 3-Clause License
 */

namespace ymaker\translatable\tests\entities;

use yii\db\ActiveRecord;
use ymaker\translatable\TranslatableBehavior;

/**
 * Product entity.
 *
 * @property int $id
 * @property string $title
 * @property string $description
 *
 * @method ProductTranslation translateTo($language = null)
 * @method ProductTranslation getTranslation($language = null)
 * @method bool hasTranslation($language = null)
 *
 * @author Vladimir Kuprienko <vldmr.kuprienko@gmail.com>
 * @since 1.0
 */
class Product extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'product';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'translatable' => [
                'class' => TranslatableBehavior::className(),
                'translationAttributeList' => [
                    'title',
                    'description',
                ],
            ],
        ];
    }

    /**
     * Relations for translations.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTranslations()
    {
        return $this->hasMany(ProductTranslation::className(), ['product_id' => 'id']);
    }
}
