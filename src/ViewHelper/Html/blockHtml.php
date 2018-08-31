<?php
namespace XT\Core\ViewHelper\Html;

/**
 * --------------
 * 
 * 
 * 1) Choose one name for key holder in view(layout) is : key-holder example : content, footer, header, sidebar ...
 * 
 * 2) Start render content at key-holder: VALUE = ''
 *    a) Trigger event [start-ahdesign-render-$name] and   VALUE .= value return
 *   
 *    b) Get value in array: [$name.'_partial_head'] and   VALUE .= value in array
 *    c) Get value in  $name and                           VALUE .= value
 * 
 *    d) Trigger event [end-ahdesign-render-$name],        VALUE .= value in array
 * 
 * 3) Whole content at keyholder render at keyholder, if view exist [key-holder]_block, content will be nest in block before render
 *    [key-holder_block] set by plugin: BlockView
 *  
 * 4) In View (phprender) call $this->xtechohtml('key-holder');
 * 
 * Referrence: xtsetevent  - set event for keyholder
 *             BlockView - set value or block for keyholder
 */

use XT\Core\Common\Common;
use XT\Core\System\KeyView;
use Zend\View\Helper\AbstractHelper;
 
class blockHtml extends AbstractHelper
{
    /**
     * @var \Zend\Http\PhpEnvironment\Request
     */
    protected $request;

    /**
     * @var \Zend\EventManager\EventManager
     */
    protected $eventmanager;


    /**
     * @param string $nameholder key holder in template
     */
    public function __invoke($nameholder)
    {
        /**
         * @var $view \PhpRenderer
         */
        $view = $this->view;

        if ($this->request == null)
            $this->request = Common::getRequest();

        if ($this->eventmanager == null)
            $this->eventmanager = Common::$em;

        $html = '';



        /**
         * $pageinfo - param will be send with event
         * 
         *       Render 3 block:
         *       ---------------
         *       - START BLOCK -     => VALUE FROM EVENT: [start-ahdesign-render-$name] - xtsetevent
         *       - MAIN BLOCK  -     => xtsetmoule(Array: $name._partial_head) + ($name value) +xtsetmoule(Array: $name._partial_head)
         *       - END BLOCK   -     => VALUE FROM EVENT: [end-ahdesign-render-$name] - xtsetevent
         *       ---------------
         */

        $pageinfo = new \ArrayObject(
            [
                'pageinfo'=> (isset($view->pageinfo))? $view->pageinfo : null,
                'view'    => $view
            ]);


        //Event Before Block
        $this->trigger(KeyView::prefix_html_start, $html, $nameholder, $pageinfo);

        $this->renderpartial($nameholder.KeyView::key_head, $html, $view);

        //Value with name
        if (isset($view->$nameholder))
            $html .= $view->$nameholder;

        $this->renderpartial($nameholder.KeyView::key_foot, $html, $view);

        //Event After Block
        $this->trigger(KeyView::prefix_html_end, $html, $nameholder, $pageinfo);

        
        $block = $nameholder.'_'.KeyView::key_block;



//        if (isset(Common::$cf->common->noloadtemplateholder))
//        {
//            Common::$cf->common->noloadtemplateholder;
//        }


        
        if (isset($view->$block)) {
            if (Common::keyholder_notemplate_exist($nameholder))
            {
                //Khong nap template hoac nap template khac
                //$html = '<div class="layout-content">'..'</div>';

            }
            else
                $html = $view->partial($view->$block, ['htmlblock'=>&$html]);
        }

        
        echo $html;
    }

    /**
     * @param string $key
     * @param string $html
     * @param \PhpRenderer $view
     */
    function renderpartial($key, &$html, &$view)
    {
        if (isset($view->$key))
        {
            $array_partial = & $view->$key->get_array();
            foreach ($array_partial as & $partial)
                $html .= $view->partial($partial['partial'],$partial['value']);
        }
    }

    /**
     * @param $prefix
     * @param $html
     * @param $nameholder
     * @param $pageinfo
     */
    function trigger($prefix, &$html, &$nameholder, &$pageinfo)
    {
        $evname = $prefix.$nameholder;

        if (DEBUG_ON)
            $html.= '<span class="badge badge-danger m-3">'.$evname.'</span>';

        $rt_event = $this->eventmanager->trigger($evname, $this, $pageinfo);

        foreach ($rt_event as $ob)
            $html .= $ob;

    }

}