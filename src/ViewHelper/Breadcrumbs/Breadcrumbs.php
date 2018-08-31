<?php

namespace XT\Core\ViewHelper\Breadcrumbs;


use Zend\View\Helper\AbstractHelper;

class Breadcrumbs extends AbstractHelper
{
    /**
     * Array of items.
     * @var array
     */
    private $items = [];

    /**
     * Constructor.
     * @param array $items Array of items (optional).
     */
    public function __construct($items=[])
    {
        $this->items = $items;
    }

    /**
     * Sets the items.
     * @param array $items Items.
     */
    public function setItems($items)
    {
        $this->items = $items;
        return $this;
    }

    public function renderRow()
    {
        return '<div class="row"><div class="col-md-12">'.$this->render().'</div></div>';
    }

    /**
     * Renders the breadcrumbs.
     * @return string HTML code of the breadcrumbs.
     */
    public function render()
    {
        if (count($this->items)==0)
            return ''; // Do nothing if there are no items.

        // Resulting HTML code will be stored in this var
        $result = '<nav class="breadcrumb">';

        // Get item count
        $itemCount = count($this->items);

        $itemNum = 1; // item counter

        // Walk through items
        foreach ($this->items as $label=>$link) {

            // Make the last item inactive
            $isActive = ($itemNum==$itemCount?true:false);

            // Render current item
            $result .= $this->renderItem($label, $link, $isActive);

            // Increment item counter
            $itemNum++;
        }

        $result .= '</nav>';

        return $result;

    }

    /**
     * Renders an item.
     * @param string $label
     * @param string $link
     * @param boolean $isActive
     * @return string HTML code of the item.
     */
    protected function renderItem($label, $link, $isActive)
    {
        $escapeHtml = $this->getView()->plugin('escapeHtml');

        if (!$isActive)
            $result = '<a class="breadcrumb-item" href="'.$escapeHtml($link).'">'.$escapeHtml($label).'</a>';
        else
            $result = '<span class="breadcrumb-item active">'.$escapeHtml($label).'</span>';

        return $result;
    }
}

