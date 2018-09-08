<?php
namespace XT\Core\Admin\Layout\Form;

use XT\Core\Common\Common;
use XT\Core\Filter\LatinLowCase;
use XT\Core\Form\Form;
use Zend\Db\Adapter\Adapter;
use Zend\Filter\StringToLower;
use Zend\Filter\StringTrim;
use Zend\Form\Element\Hidden;
use Zend\Form\Element\Text;
use Zend\InputFilter\InputFilter;
use Zend\Validator\Db\NoRecordExists;
use Zend\Validator\Db\RecordExists;
use Zend\Validator\NotEmpty;
use Zend\Validator\StringLength;

class FormHolder extends Form
{

    public function __construct($name, array $options = [])
    {
        parent::__construct($name, $options);

        $this->add([
            'name' => 'placeholder_id',
            'type' => Hidden::class
        ]);

        $this->add([
            'name' => 'name',
            'type' => Text::class,
            'attributes' => [
                'class' => 'form-control'
            ],
        ]);
        $this->add([
            'name' => 'description',
            'type' => Text::class,
            'attributes' => [
                'class' => 'form-control'
            ],
        ]);



        $inputfilter = new InputFilter();
        $inputfilter->add([
            'name' => 'name',
            'filters' => [
                ['name' => LatinLowCase::class],
                ['name' => StringTrim::class,    'options' => [ 'charlist' => " ,#,$" ]],
                ['name' => StringToLower::class, 'options' => [ 'encoding' => "UTF-8" ]],
            ],
            'validators' => [
                ['name' => NotEmpty::class],
                ['name' => StringLength::class, 'options' => [ 'encoding' => 'UTF-8', 'min' => 2, 'max' => 100, ], ]
            ],
        ]);

        $inputfilter->add([
            'name' => 'description',
            'filters' => [
                ['name' => StringTrim::class, 'options' => [ 'charlist' => " ,#,$" ] ],
            ],
            'validators' => [
                ['name' => NotEmpty::class],
                ['name' => StringLength::class, 'options' => [ 'encoding' => 'UTF-8', 'min' => 2, 'max' => 1000, ], ]
            ],
        ]);


        $this->setInputFilter($inputfilter);



    }

    /***
     * @param array $exclude ['field' => col, 'value' => val]
     */
    public function addNoRecordExistsName($exclude = null)
    {

        $validatorchain = $this->getInputFilter()->get('name')->getValidatorChain();


        $validator_checkdatabase = new NoRecordExists(
            [
                'table' => 'placeholder',
                'field' => 'name',
                'adapter' => Common::$sm->get(Adapter::class)
            ]
        );

        if ($exclude != null)
            $validator_checkdatabase->setExclude($exclude);

        $validatorchain->addValidator($validator_checkdatabase);
    }
}