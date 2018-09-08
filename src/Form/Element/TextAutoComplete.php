<?php

namespace XT\Core\Form\Element;
use Zend\Form\Element;

/**
 *
 * options: valueAutocomplete = ['val1', 'val2', 'val3']
 *
$form->add([
    'name' => 'testTextAutoComplete',
    'type' =>  TextAutoComplete::class,
    'options' => [
    'valueAutocomplete' => [
            'AAA',
            'BBBBBBBB',
            'FFFFFFXdfsdfd'
    ]
    ],
    'attributes' => [
        'class' => 'form-control',
        'id' => 'testTextAutoComplete',
    'placeholder' => 'testTextAutoComplete']]);
 */
class TextAutoComplete extends Element
{
    protected $attributes = [
        'type' => 'text'
    ];
}