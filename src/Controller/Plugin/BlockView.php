<?php

namespace XT\Core\Controller\Plugin;


use XT\Core\System\KeyView;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

class BlockView extends AbstractPlugin
{
    /**
     * @param array $option
     * @param string $placeholder
     * @param \Zend\View\Model\ViewModel $viewmodel
     * @return null
     */
    public function __invoke($option, $placeholder = KeyView::CONTENT, $viewmodel = null)
    {

        $layout = ($viewmodel) ? $viewmodel : $this->getController()->layout();

        $addvalue = function ($key, $value) use ($layout) {
            if (isset($layout->$key))
                $layout->$key .= $value;
            else
                $layout->$key = $value;
        };

        if (!is_array($option)) {
            $addvalue($placeholder, $option);
            return;
        }


        $key = key($option);
        $value = &$option[$key];

        if (DEBUG_ON)
            $this->DEBUG($key, $value, $layout, $placeholder);


        switch ($key) {

            //Add childview to VIEW
            case KeyView::key_addview:
                $layout->addChild($value, $placeholder, true);
                break;


            //Add value to Array head or footer of keyholder
            case KeyView::key_foot:
            case KeyView::key_head:
                $placeholder_partial = $placeholder . '_' . $key;
                $ar = $layout->$placeholder_partial = (!isset($layout->$placeholder_partial)) ?
                    (new ContainerParams()) : $layout->$placeholder_partial;
                $ar[] = $value;
                break;

            //Add block to keyholder
            case KeyView::key_block:
                $blockkey = $placeholder . '_' . $key;
                $layout->$blockkey = $value;
                break;

            default:
                $addvalue($placeholder, $value);
                break;
        }
    }

    /**
     * Add to FlashGlobal MSG
     * debug information
     */
    function DEBUG($key, &$value, $layout, $placeholder)
    {
        $msg = 'MODULE [' . $key . '][' . $placeholder . '] => ' . get_class($this->getController()) . ' in [' . $layout->getTemplate() . ']';
        switch ($key) {
            case 'view':
                break;

            case 'partial_head':
            case 'partial_foot':
                $msg .= '==>' . $value['partial'];
                break;
            case 'block':
                $msg .= '==>' . $value;
                break;

            default:
                $msg .= '(value HTML)';
                break;
        }

        if (!isset($this->getController()->layout()->FlashGlobal))
            $this->getController()->layout()->FlashGlobal = new \Zend\Mvc\Plugin\FlashMessenger\FlashMessenger();

        $this->getController()->layout()->FlashGlobal->addMessage($msg);
    }
}