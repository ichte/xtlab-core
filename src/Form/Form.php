<?php

namespace XT\Core\Form;


use XT\Core\Validator\Lang\TranslatorValidator;

class Form extends \Zend\Form\Form
{
    /**
     * @param  string  $name    Optional name for the element
     * @param  array            $options Optional options for the element
     */
    public function __construct($name, $options = [])
    {
        new TranslatorValidator();
        parent::__construct($name, $options);
        $this->addCsrf($name);

    }

    /**
     * Add the CSRF field
     * @param int $timeout
     */
    public function addCsrf($name, $timeout = 600)
    {

        $this->add([
            'type' => 'csrf',
            'name' => $this->getNameCsrf(),
            'options' => [
                'csrf_options' => [
                    'timeout' => $timeout
                ]
            ],
        ]);
    }

    public function getNameCsrf() {
        return 'csrf'.$this->getName();
    }

}