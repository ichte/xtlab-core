<?php

namespace XT\Core\Filter;


use Zend\Filter\Exception;
use Zend\Filter\FilterInterface;
use Zend\Filter\StaticFilter;
use Zend\I18n\Filter\Alpha;

class NameClass implements FilterInterface
{
    public function filter($value)
    {
       return StaticFilter::execute($value, Alpha::class );
    }

}