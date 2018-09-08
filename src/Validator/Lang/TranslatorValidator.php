<?php
namespace XT\Core\Validator\Lang;


use XT\Core\Common\Common;
use Zend\I18n\Translator\Translator;
use Zend\Validator\AbstractValidator;
use Zend\Validator\Translator\TranslatorInterface;

/***
 * Class TranslatorValidator
 * Use for tranlate Validators in Form
 * @package XT\Core\Validator\Lang
 */
class TranslatorValidator extends Translator implements TranslatorInterface
{

    public static $init = false;
    public function __construct()
    {
        if (!TranslatorValidator::$init)
        {

            //vi_VN
            //en_US
            $local = Common::getTranslator()->getTranslator()->getLocale();
            TranslatorValidator::$init = true;
            if ($local != 'en_US')
            {
                $this->addTranslationFile(
                    'phpArray',
                    __DIR__ . '/'.$local.'.php'
                );

                AbstractValidator::setDefaultTranslator($this);
            }


        }

    }
}