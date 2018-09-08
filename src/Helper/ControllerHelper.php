<?php

namespace XT\Core\Helper;

use XT\Core\Common\Common;


class ControllerHelper
{
    /***
     * @return array ['namecontroller' => class]
     */
    public static function controllers()
    {

        $controls= Common::$sm->get('config')['controllers'];
        $controller = [];
        foreach ($controls as $key => $cc) {
            if (!isset($controller[$key]))
                $controller[$key] = [];
            foreach ($cc as $k=>$c)
                $controller[$key][$k] =  $k;

        }
        if (!isset($controller['invokables']))
            $controller['invokables'] = [];

        $controller = array_merge(['ALL' => 'ALL',], $controller['invokables'], $controller['factories']);
        return $controller;

    }

    /***
     * @param $controllername
     * @return array
     * @throws \ReflectionException
     */

    public static function  actions($controllername) {

        if ($controllername == 'ALL')
            return ['ALL'];

        $ctrls = Common::$sm->get('ControllerManager');
        if (!$ctrls->has($controllername))  return [];
        $ct = $ctrls->get($controllername);
        $controllerClass = get_class($ct);

        $reflection = new \ReflectionClass($ct);
        $actions = ['*'];
        foreach ($reflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            $methodName = $method->getName();
            if ($methodName == 'getMethodFromAction') {
                continue;
            }
            if (substr_compare($methodName, 'Action', -strlen('Action')) === 0) {
                $actions[] = substr($methodName, 0, strlen($methodName) - 6);
            }
        }

        return $actions;

    }

    /***
     * @return array ['namecontroller-action' => 'namecontroller-action']
     * @throws \ReflectionException
     */
    public static function controller_actions() {

        $controllers = ControllerHelper::controllers();
        $ca = [];

        foreach ($controllers as $namecontroller => $class)
        {
            $actions = ControllerHelper::actions($namecontroller);

            $ctrl_act = [
                'label' => $namecontroller
            ];

            $options = [];
            foreach ($actions as $ac)
            {

                $val = $namecontroller.'-'.$ac;
                $options[$val] = $val;
            }
            $ctrl_act['options'] = $options;
            $ca[$namecontroller] = $ctrl_act;
        }

        return $ca;
    }
}