<?php

namespace XT\Core\Event\Listener;


use XT\Core\Event\AbstractListener;
use Zend\EventManager\EventInterface;

/***
 * Class BlockHtml
 * Listener render HTML for event sent in renderPlace plugin of VIEW
 * @package XT\Core\Event
 */
class BlockHtml extends AbstractListener
{
    public $blockhtml = '';

    /**
     * @param EventInterface $event : Have params [view], params[pageinfo]
     * @return string
     */
    public function execute(EventInterface $event)
    {

        $params = $event->getParams();

        if (!$this->html)
        {
            /**
             * @var \PhpRenderer $view
             */
            $view = $params['view'];
            $this->html = $view->partial($this->blockhtml, ['pageinfo' => $params['pageinfo']]);
        }
        return $this->html;
    }

    public function __invoke($servicemanager, $resolvedName, $options)
    {
        return $this->init($servicemanager, $options);
    }

    /**
     * @param string $blockhtml
     */
    public function setBlockhtml($blockhtml)
    {
        $this->blockhtml = $blockhtml;
    }
}