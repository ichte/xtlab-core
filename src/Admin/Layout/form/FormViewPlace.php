<?php

namespace XT\Core\Admin\Layout\Form;


use XT\Core\Common\Common;
use XT\Core\Form\Form;
use XT\Core\Helper\ControllerHelper;
use XT\Core\Helper\TemplateFiles;
use XT\Core\System\Placeholder\PlaceholderManager;
use Zend\Filter\StringTrim;
use Zend\Form\Element\Button;
use Zend\Form\Element\Checkbox;
use Zend\Form\Element\Hidden;
use Zend\Form\Element\Select;
use Zend\Form\Element\Text;
use Zend\InputFilter\InputFilter;
use Zend\Validator\NotEmpty;
use Zend\Validator\StringLength;

class FormViewPlace extends Form
{
    public function __construct($name, array $options = [])
    {
        parent::__construct($name, $options);

        /***
         * @var $placeholdermanager PlaceholderManager
         */
        $placeholdermanager = Common::$sm->get(PlaceholderManager::class);
        $this->add(
            [
                'name' => 'id',
                'type' => Hidden::class,
            ]
        );

        $this->add([
            'name' => 'ControllerAction',
            'type' => Select::class,
            'attributes' => [
                'class' => 'form-control'
            ],
            'options' => [

                'empty_option' => 'Select Controller and Action',
                'value_options' => ControllerHelper::controller_actions()
//                'value_options' => [
//                    [
//                        'label' => 'label',
//                        'value' => 'xxx',
//                        'attributes' =>['class' => 'text-danger']
//                    ],
//                    [
//                        'label' => 'label',
//                        'value' => 'xxx',
//                        'attributes' =>['class' => 'text-danger']
//                    ]
//                ]
            ]
        ]);


        $this->add([
            'name' => 'Event',
            'type' => Select::class,
            'attributes' => [
                'class' => 'form-control'
            ],
            'options' => [

                'empty_option' => 'Select an event',
                'value_options' => $placeholdermanager->allEvent()
            ]
        ]);


        $this->add([
            'name' => 'Class',
            'type' => Text::class,
            'attributes' => [
                'class' => 'form-control'
            ],
            'options' => [

            ]
        ]);

        $this->add(
            [
                'name' => 'active',
                'type' => Checkbox::class,

                'options' => [
                    'label' => 'Active '
                ]
            ]
        );



        $inputfilter = new InputFilter();

        $inputfilter->add(
            [
                'name' => 'Class',
                'filters' => [
                    ['name' => StringTrim::class,    'options' => [ 'charlist' => " ,#,$" ]],
                ],
                'validators' => [
                    ['name' => NotEmpty::class],
                    ['name' => StringLength::class, 'options' => [ 'encoding' => 'UTF-8', 'min' => 2, 'max' => 255, ], ]
                ],
            ]
        );

        $this->setInputFilter($inputfilter);

    }

}



