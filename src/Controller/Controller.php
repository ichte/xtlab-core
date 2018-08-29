<?php

namespace XT\Core\Controller;


use Zend\Mvc\Controller\AbstractActionController;
use Zend\ServiceManager\ServiceManager;

class Controller extends AbstractActionController
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

    
}