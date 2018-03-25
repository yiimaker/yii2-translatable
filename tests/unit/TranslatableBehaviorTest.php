<?php
/**
 * @link https://github.com/yiimaker/yii2-translatable
 * @copyright Copyright (c) 2017-2018 Yii Maker
 * @license BSD 3-Clause License
 */

namespace ymaker\translatable\tests\unit;

use ymaker\translatable\tests\entities\Product;
use ymaker\translatable\tests\entities\ProductTranslation;

/**
 * Test case for translatable behavior.
 *
 * @author Vladimir Kuprienko <vldmr.kuprienko@gmail.com>
 * @since 1.0
 */
class TranslatableBehaviorTest extends TestCase
{
    /**
     * @var Product
     */
    private $product;

    /**
     * {@inheritdoc}
     */
    protected function _before()
    {
        $this->product = new Product();
    }

    public function testCreateModel()
    {
        // english
        $this->product->title = '[en] Test title';
        $this->product->description = '[en] Test description';

        // french
        $this->product->translateTo('fr')->title = '[fr] Test title';
        $this->product->translateTo('fr')->description = '[fr] Test description';

        $this->product->insert();

        $this->tester->seeRecord(ProductTranslation::className(), [
            'language'      => 'en',
            'title'         => '[en] Test title',
            'description'   => '[en] Test description',
        ]);
        $this->tester->seeRecord(ProductTranslation::className(), [
            'language'      => 'fr',
            'title'         => '[fr] Test title',
            'description'   => '[fr] Test description'
        ]);
    }

    public function testUpdateModel()
    {
        // english
        $this->product->title = '[en] New product';
        $this->product->description = '[en] This is new product';

        // french
        $this->product->translateTo('fr')->title = '[fr] New product';
        $this->product->translateTo('fr')->description = '[fr] This is new product';

        $this->product->insert();

        $product = Product::findOne($this->product->id);

        // english
        $product->getTranslation()->title = '[en] Updated product';
        $product->getTranslation()->description = '[en] This is updated product';

        // french
        $product->translateTo('fr')->title = '[fr] Updated product';
        $product->translateTo('fr')->description = '[fr] This is updated product';

        $product->update();

        $this->tester->seeRecord(ProductTranslation::className(), [
            'language' => 'en',
            'title' => '[en] Updated product',
            'description' => '[en] This is updated product',
        ]);
        $this->tester->seeRecord(ProductTranslation::className(), [
            'language' => 'fr',
            'title' => '[fr] Updated product',
            'description' => '[fr] This is updated product',
        ]);
    }

    public function testValidationErrors()
    {
        $this->product->title = null;
        $this->product->description = null;
        $this->product->translateTo('fr')->title = null;
        $this->product->translateTo('fr')->description = null;
        $this->product->validate();

        $this->assertTrue($this->product->hasErrors());
        $this->assertSame(
            [
                'title [en]'        => ['Title cannot be blank.'],
                'description [en]'  => ['Description cannot be blank.'],

                'title [fr]'        => ['Title cannot be blank.'],
                'description [fr]'  => ['Description cannot be blank.'],
            ],
            $this->product->getErrors()
        );
    }

    public function testModelHasProperty()
    {
        $this->assertTrue($this->product->hasProperty('title'));
        $this->assertTrue($this->product->hasProperty('description'));
    }

    public function testHasTranslation()
    {
        $this->assertFalse($this->product->hasTranslation());
        $this->assertFalse($this->product->hasTranslation('fr'));

        $this->product->getTranslation('fr');
        $this->assertFalse($this->product->hasTranslation());
        $this->assertTrue($this->product->hasTranslation('fr'));
    }
}
