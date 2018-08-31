<?php
 

namespace XT\Core\ToolBox;

use XT\Core\Common;
use Zend\View\Renderer\PhpRenderer;

class MessageBox
{
    public static function viewNoPermission($event, $mgs = null)
    {

        $response  =  $event->getResponse();
        $event->stopPropagation();
        $response->setStatusCode(403);
        $response->sendHeaders();

        $phpRender = new PhpRenderer();
        $phpRender->setHelperPluginManager(Common::getViewHelper());
        $phpRender->setResolver(Common::builResolver()); 
        $response->setContent($phpRender->render('toolbox/nopermission', ['mgs' => $mgs]));
        return $response;
    }

}