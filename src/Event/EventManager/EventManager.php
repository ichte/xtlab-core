<?php

namespace XT\Core\Event\EventManager;


class EventManager extends \Zend\EventManager\EventManager
{
    /**
     * @return array[]
     */
    public function getEvents()
    {
        return $this->events;
    }

 
}