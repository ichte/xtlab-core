<?php
namespace XT\Core\Common;

use XT\Core\Event\BlockHtml;
use XT\Core\System\Bags;
use Zend\EventManager\EventManager;
use Zend\EventManager\LazyListener;
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
     * @var \XT\Core\Event\EventManager\EventManager
     */
    public static $em;

    /**
     * @var Zend\Mvc\Application
     */
    public static $app;

    /**
     * Acesss by $cf->CF-> ...
     * @var \Options;
     */
    public static $cf;

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



    /**
     * Register Event for xtecho
     * @param string $eventname
     * @param string $class | is Listener -> class call for render : ProductCategory\, BlockHtml
     * @param null $renderblock | name block use in BlockHtml
     */
    public static function register_event($eventname, $class= BlockHtml::class, $renderblock = null)
    {
        $serviceManager = self::$sm;
        $eventManager   = self::$em;


        if ($renderblock)
            $class = BlockHtml::class;

        $listener_key = $class.($renderblock?'-renderblock:'.$renderblock:'');



        //Create Listerner render Error for Event if Listener Class not exist
        if (!class_exists($class))
        {
            $renderEventNotExist = function ($e) use ($class, $eventname) {
                return "<span class='badge badge-danger m-3'>Listener <b>$class</b> for event <i>$eventname</i> not exists!</span>";
            };
            $eventManager->attach($eventname,$renderEventNotExist );
            return;
        }


        if (!$serviceManager->has($listener_key))
            $serviceManager->setFactory($listener_key, $class);

        $ars = ($renderblock) ? ['blockhtml' => $renderblock] : [];

       

        $eventManager->attach($eventname, new LazyListener(
            [
             'listener' => $listener_key,
             'method'   => 'execute',
            ],
            $serviceManager, $ars));
    }


}