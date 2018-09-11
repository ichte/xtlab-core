<?php

namespace XT\Core\Admin\Layout\Form;


use XT\Core\Common\Common;
use XT\Core\Form\Form;
use XT\Core\Helper\ControllerHelper;
use XT\Core\Helper\TemplateFiles;
use XT\Core\System\Placeholder\PlaceholderManager;
use Zend\Form\Element\Button;
use Zend\Form\Element\Checkbox;
use Zend\Form\Element\Hidden;
use Zend\Form\Element\Select;
use Zend\InputFilter\InputFilter;

class FormInsertBlock extends Form
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
            'name' => 'placeholder',
            'type' => Select::class,
            'attributes' => [
                'class' => 'form-control'
            ],
            'options' => [

                'empty_option' => 'Select an event',
                'value_options' => $placeholdermanager->allHolderSelectElements()
            ]
        ]);


        $this->add([
            'name' => 'Block',
            'type' => Select::class,
            'attributes' => [
                'class' => 'form-control'
            ],
            'options' => [

                'empty_option' => 'Select a file .phtml',
                'value_options' => TemplateFiles::listfiles()
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



//        $inputfilter = new InputFilter();
//
//        $inputfilter->add(
//            [
//
//            ]
//        );
//
//        $this->setInputFilter($inputfilter);

    }

}



