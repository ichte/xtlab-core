<?php

namespace XT\Core\Controller;


use XT\Core\Common\Common;
use XT\Core\Controller\Plugin\askBeforeDone;
use XT\Core\Controller\Plugin\isConfirm;
use XT\Core\Controller\Plugin\isGranted;
use Zend\EventManager\EventInterface as Event;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\ServiceManager\ServiceManager;

/***
 * Class Controller
 * @package XT\Core\Controller
 * @method isGranted isGranted($permission, $assert = null, $optionassert = [], $user = null)
 * @method askBeforeDone    askBeforeDone($title, $urlpost, $elementform = [])
 * @method isConfirm        isConfirm($fiels)
 */
abstract class Controller extends AbstractActionController
{
    /**
     * @var ServiceManager
     */
    protected  $serviceManager;

    /**
     * @param $serviceManager ServiceManager
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