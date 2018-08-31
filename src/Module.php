<?php
namespace XT\Core;

use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\ServiceManager;
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
        /***
         * @var $serviceManager ServiceManager
         */
        Common::$sm             = $e->getApplication()->getServiceManager();
        Common::$app            = $e->getApplication();
        Common::$em             = $e->getApplication()->getEventManager();


        //Init Session
        $coresession = Common::$sm->get('config')['coresession'];
        $sessionConfig = new SessionConfig();
        $sessionConfig->setOptions($coresession);
        $sessionManager = new SessionManager($sessionConfig);
        Container::setDefaultManager($sessionManager);
        $sessionManager->start();
    }
}