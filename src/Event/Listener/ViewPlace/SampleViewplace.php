<?php
namespace XT\Core\Event\Listener\ViewPlace;


use XT\Core\Event\AbstractListener;
use Zend\EventManager\EventInterface;

class SampleViewplace extends AbstractListener
{
    public function execute(EventInterface $event)
    {
        return "<span class='badge badge-danger'>".__CLASS__."</span>";


    }

    public function __invoke($servicemanager, $resolvedName, $options)
    {

        parent::__invoke($servicemanager, $resolvedName, $options);
        return $this;
    }




}