<?php
namespace XT\Core\Form\Element;
use Zend\Form\Element;

/***
 * Class TextareaAutoGrow
 * @package XT\Core\Form\Element
 *
 * Example:
 *
 *
        $this->add([
            'name' => 'body',
            'type' => TextareaAutoGrow::class,
            'attributes' => [
                'class' => 'form-control',
                'id' => 'body'
            ],
            'options' => [
                'required' => true,
                'minheight'=>50,
                'label' => 'Label'
            ],
        ]);

 * TextareaAutoGrow render by \XT\Core\Form\Helper\FormTexarea in \XT\Core\Form\Helper\FormElement
 */
class TextareaAutoGrow extends Element\Textarea
{

}