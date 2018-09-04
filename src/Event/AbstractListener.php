<?php
namespace  XT\Core\Event;

use Zend\Cache\Storage\Adapter\AbstractAdapter;
use Zend\EventManager\EventInterface;
use Zend\ServiceManager\ServiceManager;

abstract class AbstractListener
{
    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    /**
     * @var AbstractAdapter
     */
    protected $cache;

    protected $html = null;

    public function execute(EventInterface $event)
    {

    }

    public function init($servicemanager, $options = null)
    {
        $this->serviceManager = $servicemanager;
        $this->cache = $servicemanager->get('CacheCommon');

        if ($options != null)
            foreach ($options as $property => $option) {

                $setter = 'set'.$property;
                $this->$setter($option);
            }
        return $this;
    }
}