<?php

namespace XT\Core\Controller\Plugin;


use XT\Core\Common\Common;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Stdlib\DispatchableInterface as Dispatchable;

class isGranted extends AbstractPlugin
{

    var $RbacManager = null;
    /**
     * Checks whether the given user has permission.
     * @param User|null $user
     * @param string $permission
     * @param AssertionAbstract|string|null $assert
     */
    public function __invoke($permission, $assert = null, $optionassert = [], $user = null)
    {
        return Common::isGranted($permission, $assert, $optionassert, $user);
    }

}