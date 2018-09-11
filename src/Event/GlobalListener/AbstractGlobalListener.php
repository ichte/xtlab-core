<?php

namespace XT\Core\Event\GlobalListener;


use Psr\Container\ContainerInterface;
use Zend\EventManager\Event;
use Zend\ServiceManager\ServiceManager;

abstract class AbstractGlobalListener
{
    /***
     * @var ServiceManager
     */
    protected $serviceManager;


    /***
     * @param Event $event
     * @return mixed
     */
    public abstract function execute($event);

    /***
     * @param $sm ContainerInterface
     * @return mixed
     */
    public abstract function  __invoke($sm);

}