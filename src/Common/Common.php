<?php
namespace XT\Core\Common;

use XT\Core\System\Bags;
use Zend\View\HelperPluginManager;
use Zend\View\Resolver\AggregateResolver;
use Zend\View\Resolver\TemplateMapResolver;
use Zend\View\Resolver\TemplatePathStack;
use Zend\ServiceManager\ServiceManager;
use Zend\Mvc\I18n\Translator;

class Common
{

    use ViewHelperHeader;
    use ViewHelperPhpRender;
    use User;
    use Url;
    /**
     * @var ServiceManager
     */
    public static $sm;
    /**
     * @var \Zend\EventManager\EventManager
     */
    public static $em;

    /**
     * @var Zend\Mvc\Application
     */
    public static $app;
    
    /**
     * @return \Zend\Http\PhpEnvironment\Request
     */
    public static function getRequest()
    {
        return self::$sm->get('request');

    }


    public static $_bags;
    /**
     * @return Bags
     */
    public static function getBags() {
        return (self::$_bags == null)?
            (self::$_bags = new Bags()) : (self::$_bags);
    }

    /***
     * @return Translator
     */
    public static function getTranslator() {

        return self::$sm->get('MvcTranslator');
    }


    public static function translate($mgs) {

        return self::getTranslator()->translate($mgs);
    }

    public static function error_handler($errno, $errstr, $errfile, $errline)
    {
        $mgs = '(ERR) '.$errstr.'|'.$errfile.'('.$errline.')';
        throw new \Exception($mgs);

    }

    public static function set_error_handler()
    {
        set_error_handler(array(__CLASS__, 'error_handler'));
    }

    public static function logErr($log) {
        /**
         * @var $logger \Zend\Log\Logger
         */

         $logger = self::$sm->get('xtlab_err_log');
         $logger->debug($log.'|'.self::getUrlCanonical());
    }

    
}