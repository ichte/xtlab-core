<?php
/**
 * Created by PhpStorm.
 * User: Dao Xuan Thu
 * Date: 01-Sep-18
 * Time: 11:20 PM
 */

namespace XT\Core\Controller\Plugin;


use Zend\Mvc\Controller\Plugin\AbstractPlugin;

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


        if ($request->isPost()) {
            if ($fielValues != null)
            {
                $params = $ctrl->params();
                foreach ($fielValues as $key => $fielValue) {
                    if ($params->fromPost($key, null) != $fielValue) return false;
                }

                if ($ctrl->params()->fromPost('buttonask', null) == 'OK') return true;
            }



        }
        return false;

    }
}