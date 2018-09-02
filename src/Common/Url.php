<?php

namespace XT\Core\Common;

use Zend\Router\Http\RouteMatch;
trait Url
{

    public static $_urlfull;
    public static $_routematch;

    /**
     * @return RouteMatch
     */
    public static function RouteMatch() {
        if (!self::$_routematch) {
            self::$_routematch = self::$sm->get('router')->match(self::$sm->get('request'));
        }
        return self::$_routematch;
    }

    /**
     * @return string
     */
    public static function getUrlCanonical()
    {
        if (self::$_urlfull == null)
        {
            self::$_urlfull = self::$sm->get('ControllerPluginManager')
                ->get('url')
                ->fromRoute(self::RouteMatch()
                    ->getMatchedRouteName(),[],['force_canonical'=>true],true);
        }

        return self::$_urlfull;
    }
}