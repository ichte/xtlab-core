<?php


namespace XT\Core\ViewHelper\Menu;


use Zend\View\Helper\AbstractHelper;

class DropdownMenu extends AbstractHelper
{
    protected $icon = '<i class="fa fa-sitemap" aria-hidden="true"></i>';
    protected $name = 'Quick Access';


    /**
     * @param string $icon
     */
    public function setIcon(string $icon)
    {
        $this->icon = $icon;
        return $this;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
        return $this;
    }

    public function render($items = [])
    {
        $escapeHtml = $this->getView()->plugin('escapeHtml');
        $escapeUrl  = $this->getView()->plugin('escapeUrl');
        $li = '';
        foreach ($items as $key => $val) {
            if ($key=='divider')
                $li .= '<div class="dropdown-divider"></div>';
            else
                $li .= '<a class="dropdown-item" href="'.$escapeHtml($val).'">'.$escapeHtml($key).'</a>';


        }
$ul=<<<"HTML"
                <div class="btn-group mb-1">
                    <button type="button" class="btn btn-seconday dropdown-toggle btn-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        {$this->icon} {$this->name}
                    </button>
                    <div class="dropdown-menu">$li</div>
                </div>
HTML;
       return $ul;
    }

}

