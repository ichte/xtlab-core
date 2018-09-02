<?php
 

namespace XT\Core\ToolBox;

use XT\Core\Common\Common;
use Zend\View\Model\ViewModel;
use Zend\View\Renderer\PhpRenderer;

class MessageBox
{
    public static function redirectMgs($mgs = null,  $url = '', $title = '', $second = 10)
    {

        $mgs_display = null;
        if (is_array($mgs))
            $mgs_display = $mgs;
        else
            $mgs_display = [$mgs];

        $view = new ViewModel(
            [
                'url_redirect' => $url,
                'mgs' => $mgs_display,
                'title' => $title,
                'second' => $second
            ]
        );

        $view->setTemplate('toolbox/redirectmessage.phtml');
        return $view;
    }

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

    public static function htmlMessage($message = 'Notice from website', $title='Notification') {
        $view = new ViewModel([
            'title' => $title,
            'message' => $message
        ]);
        $view->setTemplate('toolbox/notify_html');
        return $view;
    }

}