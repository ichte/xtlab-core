<?php

namespace XT\Core\Admin\Layout\Form;


use XT\Core\Common\Common;
use XT\Core\Filter\NameClass;
use XT\Core\Form\Element\TextareaAutoGrow;
use XT\Core\Form\Form;
use XT\Core\Helper\ControllerHelper;
use XT\Core\Helper\TemplateFiles;
use XT\Core\System\Placeholder\PlaceholderManager;
use Zend\Form\Element\Button;
use Zend\Form\Element\Checkbox;
use Zend\Form\Element\Hidden;
use Zend\Form\Element\Select;
use Zend\Form\Element\Text;
use Zend\InputFilter\InputFilter;
use Zend\Validator\NotEmpty;
use Zend\Validator\StringLength;

class FormGlobalListener extends Form
{
    public function __construct($name, array $options = [])
    {
        parent::__construct($name, $options);


        $this->add([
            'name' => 'id',
            'type' => 'Hidden',])
            ->add([
                'name' => 'classname',
                'type' => Text::class,
                'options'=> [
                    'label'=>'Name Listner Global',
                    'label_attributes'=>['class'=>'col-2 col-form-label']

                ],
                'attributes' => ['id'=>'classname', 'class' => 'form-control col-10', 'placeholder' => 'Enter name of class']])
            ->add([
                'name' => 'event',
                'type' => Text::class,
                'options'=> [
                    'label'=>'Events',
                    'label_attributes'=>['class'=>'col-2 col-form-label']

                ],
                'attributes' => ['id'=>'event', 'class' => 'form-control col-10', 'placeholder' => 'List events, separate by |']])
            ->add([
                'name' => 'description',
                'type' => TextareaAutoGrow::class,
                'options'=> [
                    'label'=> 'Description',
                    'label_attributes' => ['class'=>'col-2 col-form-label']
                ],
                'attributes' => ['class' => 'form-control col-10', 'id'=>'description', 'placeholder' => 'description']])
            ->add([
                'name' => 'code',
                'type' => 'Textarea',
                'options'=> [
                    'label'=> 'Code execute',
                    'label_attributes' => ['class'=>'col-2 col-form-label']
                ],
                'attributes' => ['class' => 'form-control col-10', 'id'=>'code']])

            ->add([
                'name' => 'active',
                'type' => 'checkbox',
                'options'=> ['label'=>'Active  ', 'label_attributes' => ['class'=>'col-10 col-form-label']]]);


            $this->getInputFilter()
            ->add([
                'name'=> 'classname',
                'filters'  => [
                    [
                        'name' => 'StringTrim',
                        'options'=>['charlist'=>" ,#,$"]
                    ],
                    ['name'=> NameClass::class]
                ],
                'validators' => [

                    ['name'    => NotEmpty::class],
                    [
                        'name'    => StringLength::class,
                        'options' => [
                            'encoding' => 'UTF-8',
                            'min'      => 3,
                            'max'      => 250,
                        ],
                    ]
                ]
            ]);

    }

}



