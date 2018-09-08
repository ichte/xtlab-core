<?php
/**
 * Created by PhpStorm.
 * User: Dao Xuan Thu
 * Date: 01-Sep-18
 * Time: 11:20 PM
 */

namespace XT\Core\Controller\Plugin;


use XT\Core\Validator\Lang\TranslatorValidator;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Validator\Csrf;

class isConfirm extends AbstractPlugin
{
    /**
     * @param array $fielValues
     * @return bool
     */
    public function __invoke($fielValues = null)
    {
        $ctrl = $this->getController();
        $request = $ctrl->getRequest();
        $params = $ctrl->params();


        if ($request->isPost()) {

            $csrfaskbeforedone = new \Zend\Form\Element\Csrf('csrfaskbeforedone',['csrf_options'=>['timeout'=>'20']] );
            $pascsrf = $csrfaskbeforedone->getCsrfValidator()->isValid($params->fromPost('csrfaskbeforedone', null));
            if (!$pascsrf) {
                foreach ($csrfaskbeforedone->getCsrfValidator()->getMessages() as $mgs) {
                    echo $mgs;
                }
                die;
            }

            if ($fielValues != null)
            {

                foreach ($fielValues as $key => $fielValue) {
                    if ($params->fromPost($key, null) != $fielValue) return false;
                }

                if ($ctrl->params()->fromPost('buttonask', null) == 'OK') return true;
            }



        }
        return false;

    }
}