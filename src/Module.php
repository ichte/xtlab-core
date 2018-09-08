<?php
namespace XT\Core;

use XT\Option\Service\Factory\OptionAccess;
use XT\Option\Service\OptionManager;
use Zend\Debug\Debug;
use Zend\EventManager\EventManager;
use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\Tool\ConfigDumper;
use Zend\Session\Config\SessionConfig;
use Zend\Session\Container;
use Zend\Session\SessionManager;
use XT\Core\Common\Common;

class Module
{
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function onBootstrap(MvcEvent $e)
    {
        Common::set_error_handler();
        /***
         * @var $serviceManager ServiceManager
         */
        Common::$sm             = $e->getApplication()->getServiceManager();
        Common::$app            = $e->getApplication();
        Common::$em             = $e->getApplication()->getEventManager();
        Common::$cf             = Common::$sm ->get(OptionAccess::class);

        //INIT / START SESSION
        $coresession = Common::$sm->get('config')['coresession'];
        $sessionConfig = new SessionConfig();
        $sessionConfig->setOptions($coresession);
        $sessionManager = new SessionManager($sessionConfig);
        Container::setDefaultManager($sessionManager);
        $sessionManager->start();


        //LISTENER DISPATCH
        Common::$em->attach("dispatch", function(MvcEvent $e) { Module::onDispatchController($e); }, -100); //-100


    }

    public static function onDispatchController(MvcEvent $e) {

        $target = $e->getTarget();


        //SET BLOCK HTML BY EVENT
        $routeMatch      = $e->getRouteMatch();
        $controller_name =  $routeMatch->getParam('controller');
        $action_name     = $routeMatch->getParam('action');

        //Function Register Event

        $regter_listener = function($controller_name, $action_name, &$params_listener, $setter)
        {
            if (isset($params_listener[$controller_name]))
            {
                $cf1 = &$params_listener[$controller_name];
                if (isset($cf1[$action_name]))
                {
                    $ar_events = &$cf1[$action_name];
                    foreach ($ar_events as $eventname => &$class_listener)
                    {
                        $setter($eventname,$class_listener);
                    }
                }
            }
        };


        $config_merge = null;
        if (file_exists('config/listener_merge.cache1'))
        {
            $config_merge = unserialize(file_get_contents('config/listener_merge.cache'));
        }
        else
        {
            $config_merge['insert_html']   = file_exists('config/listener_insert_html.php')   ? include 'config/listener_insert_html.php' : [];
            $config_merge['insert_layout'] = file_exists('config/listener_insert_layout.php') ? include 'config/listener_insert_layout.php' : [];
            $config_merge['insert_plugin'] = file_exists('config/listener_insert_plugin.php') ? include 'config/listener_insert_plugin.php' : [];
            $byte = file_put_contents('config/listener_merge.cache', serialize($config_merge));
            if ($byte === false)  throw new \Exception("Can not save: config/listener_merge.cache");
        }




        //INSERT HTML LISTENER
        $regter_listener(
            'ALL',
            'ALL',
            $config_merge['insert_html'],
            function($event_name,&$phtml) use($e) {
                Common::register_event($event_name, null, $phtml);
            });


        $regter_listener(
            $controller_name,
            $action_name,
            $config_merge['insert_html'],
            function($event_name,&$phtml) use($e) {
                Common::register_event($event_name, null, $phtml);
            });

        $regter_listener(
            $controller_name,'*',
            $config_merge['insert_html'],
            function($event_name,&$phtml) use($e) { 
                Common::register_event($event_name, null, $phtml);
            });


        //SET BLOCK HTML LAYOUT
        $regter_listener(
            'ALL',
            'ALL',
            $config_merge['insert_layout'],
            function($k,&$v) use($e) {

                $e->getTarget()->setBlockView(['block' => $v],$k);
            });


        $regter_listener(
            $controller_name,
            $action_name,
            $config_merge['insert_layout'],
            function($k,&$v) use($e) {
                $e->getTarget()->setBlockView(['block' => $v],$k);
            });

        $regter_listener($controller_name, '*',
            $config_merge['insert_layout'],
            function($k,&$v) use($e) {$e->getTarget()->setBlockView(['block' => $v],$k);});

//        if ($action_name == 'not-found') {
//            $regter_listener('ALL', 'notfound', $config_merge['insert_layout'], function($k,&$v) use($e) {$e->getTarget()->setBlockView(['block' => $v],$k);});
//        }
//

//
//
//
//        //$cf = include 'config/plugin-config.php';
//        $regter_listener('ALL','ALL',  $config_merge['insert_plugin'],
//            function($event_name,&$listenerclass) use($e) { Common::register_event($event_name, $listenerclass);});
//        $regter_listener($controller_name,$action_name,$config_merge['insert_plugin'],
//            function($event_name,&$listenerclass) use($e) { Common::register_event($event_name, $listenerclass);});
//        $regter_listener($controller_name,'*',$config_merge['insert_plugin'],
//            function($event_name,&$listenerclass) use($e) { Common::register_event($event_name, $listenerclass);});


    }
}