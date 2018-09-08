<?
namespace XT\Core\Form\Helper;

use \Zend\Form\View\Helper\FormElementErrors as OriginalFormElementErrors;


class FormElementErrors extends OriginalFormElementErrors  
{
    protected $messageCloseString     = '</li></ul>';
    protected $messageOpenFormat      = '<ul%s class="text-danger p-2 pl-4"><li>';
    protected $messageSeparatorString = '</li><li>';
}