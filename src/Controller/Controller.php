<?php

namespace XT\Core\Controller;


use XT\Core\Common\Common;
use Zend\EventManager\EventInterface as Event;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\ServiceManager\ServiceManager;

abstract class Controller extends AbstractActionController
{
    /**
     * @var ServiceManager
     */
    protected  $serviceManager;

    /**
     * @param $serviceManager \Zend\ServiceManager\ServiceManager
     * @return $this
     */
    public function init($serviceManager)
    {
        $this->serviceManager   =  $serviceManager;
        return $this;
    }

    public function defaultHeader()
    {
        Common::defaultHeader();
    }
}