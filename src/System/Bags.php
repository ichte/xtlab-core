<?php
namespace XT\Core\System;

/***
 * Class Bags
 * @package XT\Core\Libs
 * @property string Container
 */

class Bags
{

    protected $value = [];


    public function __construct()
    {
        $this->Container = 'class="container containerxtlab"';
    }

    public function set($key, $value) {
        $this->value[$key] = $value;
    }
    public function get($key, $valdefault = null) {
        if (isset($this->value[$key]))
            return $this->value[$key];

        return $valdefault;
    }

    public function __get($name)
    {
        return $this->get($name);
    }

    public function __set($name, $value)
    {
        $this->value[$name] = $value;
    }

    public function addMgs($mgs)
    {
        if (!isset($this->value['Messages']))
            $this->value['Messages'] = [];
        $this->value['Messages'][] = $mgs;
    }

}