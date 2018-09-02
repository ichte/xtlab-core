<?php


namespace XT\Core\Common;


trait User
{
    public static $rbacManager;

    /**
     * Checks whether the given user has permission.
     * @param User|null $user
     * @param string $permission
     * @param AssertionAbstract|string|null $assert
     */
    public static function isGranted($permission, $assert = null, $optionassert = [], $user = null)
    {
        if (self::$rbacManager == null) {
            if (self::$sm->has('XT\User\Services\RbacManager'))
                self::$rbacManager = self::$sm->get('XT\User\Services\RbacManager');
            else return true;
        }

        return self::$rbacManager->isGranted($permission, $assert, $optionassert, $user);

    }
}