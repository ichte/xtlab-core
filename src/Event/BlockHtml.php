<?php

namespace XT\Core\Event;


use Zend\EventManager\EventInterface;

/***
 * Class BlockHtml
 * @package XT\Core\Event
 */
class BlockHtml extends AbstractListener
{
    public $blockhtml = '';

    /**
     * @param EventInterface $event : Have param [view]
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