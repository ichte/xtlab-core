<?php

namespace XT\Core\Controller;


use XT\Core\Common\Common;
use XT\Core\Controller\Plugin\askBeforeDone;
use XT\Core\Controller\Plugin\BlockView;
use XT\Core\Controller\Plugin\isConfirm;
use XT\Core\Controller\Plugin\isGranted;
use XT\Core\System\KeyView;
use Zend\EventManager\EventInterface as Event;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\ServiceManager\ServiceManager;
use Zend\Http\PhpEnvironment\Request;

/***
 * Class Controller
 * @package XT\Core\Controller
 *
 * @method isGranted        isGranted($permission, $assert = null, $optionassert = [], $user = null)
 * @method askBeforeDone    askBeforeDone($title, $urlpost, $elementform = [])
 * @method isConfirm        isConfirm($fiels)
 * @method Request          getRequest()
 * @method BlockView        setBlockView($option, $placeholder = KeyView::CONTENT, $viewmodel = null)
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