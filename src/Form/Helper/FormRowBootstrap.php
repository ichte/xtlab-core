<?php
namespace XT\Core\Form\Helper;


use Zend\Form\ElementInterface;

class FormRowBootstrap extends \Zend\Form\View\Helper\FormElement
{
    public function render(ElementInterface $element)
    {
        return '<div class="form-group">'.
            $this->renderHelper('formRow', $element).
            '</div>';

    }
}