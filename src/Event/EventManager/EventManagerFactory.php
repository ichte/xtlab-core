<?php
namespace XT\Core\Event\EventManager;


use Interop\Container\ContainerInterface;
use Zend\ServiceManager\ServiceLocatorInterface;


class EventManagerFactory extends \Zend\Mvc\Service\EventManagerFactory
{
    public function __invoke(ContainerInterface $container, $name, array $options = null)
    {
        return new EventManager(
            $container->has('SharedEventManager') ? $container->get('SharedEventManager') : null
        );
    }


    /**
     * Create and return EventManager instance
     *
     * For use with zend-servicemanager v2; proxies to __invoke().
     *
     * @param ServiceLocatorInterface $container
     * @return EventManager
     */
    public function createService(ServiceLocatorInterface $container)
    {
        return $this($container, EventManager::class);
    }
    

}