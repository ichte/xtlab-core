<?php
namespace XT\Core\Common;


use Zend\View\Resolver\AggregateResolver;
use Zend\View\Resolver\TemplateMapResolver;
use Zend\View\Resolver\TemplatePathStack;

trait ViewHelperPhpRender
{

    /**
     * @return TemplateMapResolver
     */
    public static function getTemplateMapResolver()
    {
        return self::$sm->get(TemplateMapResolver::class);
    }

    /***
     * @return TemplatePathStack
     */
    public static function getTemplatePathStack()
    {
        return self::$sm->get(TemplatePathStack::class);
    }

    /***
     * @return AggregateResolver
     */
    public static function builResolver()
    {
        $resolver = new AggregateResolver();
        return $resolver
            ->attach(self::getTemplateMapResolver())
            ->attach(self::getTemplatePathStack()); 
    }

}