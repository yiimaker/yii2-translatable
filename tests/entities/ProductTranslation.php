<?php
/**
 * @link https://github.com/yiimaker/yii2-translatable
 * @copyright Copyright (c) 2017-2018 Yii Maker
 * @license BSD 3-Clause License
 */

namespace ymaker\translatable\tests\entities;

use yii\db\ActiveRecord;

/**
 * Product translation entity.
 *
 * @property int    $id
 * @property int    $product_id
 * @property string $language
 * @property string $title
 * @property string $description
 *
 * @author Vladimir Kuprienko <vldmr.kuprienko@gmail.com>
 * @since 1.0
 */
class ProductTranslation extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'product_translation';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['title', 'required'],
            ['title', 'string'],

            ['description', 'required'],
            ['description', 'string'],
        ];
    }
}
