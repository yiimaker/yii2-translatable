<?php
/**
 * @link https://github.com/yiimaker/yii2-translatable
 * @copyright Copyright (c) 2017-2018 Yii Maker
 * @license BSD 3-Clause License
 */

namespace ymaker\translatable;

use Yii;
use yii\base\Behavior;
use yii\base\Model;
use yii\db\BaseActiveRecord;

/**
 * Behavior aggregates logic of linking translations to the primary model.
 *
 * @author Vladimir Kuprienko <vldmr.kuprienko@gmail.com>
 * @since 1.0
 */
class TranslatableBehavior extends Behavior
{
    /**
     * The owner of this behavior.
     *
     * @var BaseActiveRecord
     */
    public $owner;

    /**
     * Name of translation relation in main model.
     *
     * @var string
     */
    public $translationRelationName = 'translations';

    /**
     * Name of attribute in translation model that contains language.
     *
     * @var string
     */
    public $translationLanguageAttrName = 'language';

    /**
     * List of attribute names from translation model that should be translated.
     *
     * @var string[]
     */
    public $translationAttributeList;

    /**
     * Patter for the attribute name in validation errors list.
     *
     * @var string
     */
    public $attributeNamePattern = '%name% [%language%]';

    /**
     * Temp storage for translation entity objects.
     *
     * @var array
     */
    protected $translationsBuffer = [];


    /**
     * Translate model to needed language.
     *
     * Alias for @see getTranslation() method.
     *
     * @param null|string $language
     *
     * @return \yii\db\ActiveRecord|BaseActiveRecord
     */
    final public function translateTo($language = null)
    {
        return $this->getTranslation($language);
    }

    /**
     * Returns translation entity object for needed language.
     *
     * @param null|string $language By default uses application current language.
     *
     * @return \yii\db\ActiveRecord|BaseActiveRecord
     */
    public function getTranslation($language = null)
    {
        $language = $language ?: Yii::$app->language;
        $translations = $this->getModelTranslations();

        // search translation by language in exists translations
        foreach ($translations as $translation) {
            // if translation exists - return it
            if ($translation->getAttribute($this->translationLanguageAttrName) === $language) {
                $this->translationsBuffer[] = $translation;

                return $translation;
            }
        }

        // if translation doesn't exist - create and return
        $translationEntityClass = $this->owner->getRelation($this->translationRelationName)->modelClass;

        /* @var BaseActiveRecord $translation */
        $translation = new $translationEntityClass();
        $translation->setAttribute($this->translationLanguageAttrName, $language);
        $translations[] = $translation;
        $this->translationsBuffer = $translations;
        $this->owner->populateRelation($this->translationRelationName, $translations);

        return $translation;
    }

    /**
     * Check whether translation exists.
     *
     * @param null|string $language By default uses application current language.
     *
     * @return bool
     */
    public function hasTranslation($language = null)
    {
        $language = $language ?: Yii::$app->language;

        foreach ($this->getModelTranslations() as $translation) {
            if ($translation->getAttribute($this->translationLanguageAttrName) === $language) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function events()
    {
        return [
            BaseActiveRecord::EVENT_AFTER_VALIDATE => 'afterValidate',
            BaseActiveRecord::EVENT_AFTER_INSERT => 'afterSave',
            BaseActiveRecord::EVENT_AFTER_UPDATE => 'afterSave',
        ];
    }

    /**
     * Triggers after validation of the main model.
     */
    public function afterValidate()
    {
        $translations = $this->getModelTranslations();

        $isValid = Model::validateMultiple($translations, $this->translationAttributeList);

        if (!$isValid) {
            foreach ($translations as $translation) {
                foreach ($translation->getErrors() as $attribute => $errors) {
                    $attribute = strtr($this->attributeNamePattern, [
                        '%name%' => $attribute,
                        '%language%' => $translation->{$this->translationLanguageAttrName},
                    ]);

                    if (is_array($errors)) {
                        foreach ($errors as $error) {
                            $this->owner->addError($attribute, $error);
                        }
                    } else {
                        $this->owner->addError($attribute, $errors);
                    }
                }
            }
        }
    }
    
    /**
     * Triggers after saving of the main model.
     */
    public function afterSave()
    {
        foreach ($this->translationsBuffer as $translation) {
            $this->owner->link($this->translationRelationName, $translation);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function canGetProperty($name, $checkVars = true)
    {
        return in_array($name, $this->translationAttributeList)
            ?: $this->owner->canGetProperty($name, $checkVars);
    }
    
    /**
     * {@inheritdoc}
     */
    public function canSetProperty($name, $checkVars = true)
    {
        return in_array($name, $this->translationAttributeList)
            ?: $this->owner->canSetProperty($name, $checkVars);
    }

    /**
     * {@inheritdoc}
     */
    public function __get($name)
    {
        $translation = $this->getTranslation();

        if ($translation->hasAttribute($name)) {
            return $translation->getAttribute($name);
        }

        return $this->owner->$name;
    }

    /**
     * {@inheritdoc}
     */
    public function __set($name, $value)
    {
        $translation = $this->getTranslation();

        if ($translation->hasAttribute($name)) {
            $translation->setAttribute($name, $value);
        }

        $this->owner->$name = $value;
    }

    /**
     * @return \yii\db\ActiveRecord[]
     */
    protected function getModelTranslations()
    {
        return $this->owner->{$this->translationRelationName};
    }
}
