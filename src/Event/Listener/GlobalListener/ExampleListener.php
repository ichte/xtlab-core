<?php

namespace XT\Core\Event\Listener\GlobalListener;

/**
 * Class ExampleListener
 *
 * First example global listener
 */
class ExampleListener extends \XT\Core\Event\GlobalListener\AbstractGlobalListener
{

    /**
     * Execute when receive events
     *
     *
     * @param \Zend\EventManager\Event $event
     * @return array|\ArrayAccess|mixed|object
     */
    public function execute($event)
    {
        echo __CLASS__;
    }

    public function __invoke($serviceManager)
    {
        $this->serviceManager = $serviceManager;
        return $this;
    }


}