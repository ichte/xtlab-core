<?php

namespace XT\Core\Admin\Layout;



use XT\Admin\Controller\AbstractPlugin;
use XT\Core\Admin\Layout\Form\FormHolder;
use XT\Core\Admin\Layout\Form\FromInsertHtml;
use XT\Core\Common\Common;
use XT\Core\Controller\Controller;
use XT\Core\Event\BlockLayout\BlockLayoutManager;
use XT\Core\Event\InsertHtml\InsertHTML;
use XT\Core\Event\InsertHtml\InsertHtmlManager;
use XT\Core\Helper\ControllerHelper;
use XT\Core\Helper\TemplateFiles;
use XT\Core\System\Placeholder\Placeholder;
use XT\Core\System\Placeholder\PlaceholderManager;
use XT\Core\ToolBox\MessageBox;
use XT\Core\System\RBAC_PERMISSION;
use XT\Core\Validator\ExtensionAllow;
use Zend\Filter\FilterChain;
use Zend\Filter\PregReplace;
use Zend\Filter\StringToLower;
use Zend\Filter\StringTrim;
use Zend\Form\Element\Checkbox;
use Zend\Form\Element\Select;
use Zend\I18n\Filter\Alpha;
use Zend\Validator\File\Extension;
use Zend\Validator\File\NotExists;
use Zend\Validator\NotEmpty;
use Zend\Validator\StringLength;
use Zend\Validator\ValidatorChain;
use Zend\View\Model\ViewModel;

class Layout extends AbstractPlugin
{



    /**
     * @var ValidatorChain
     */
    protected $validator;

    protected $filter;

    protected $nameplugin = 'Layout';

    protected $description = 'Manager layout (.phtml)';

    public function intPlugin()
    {
        $ar = [];

        $ar['placeholder'] = [
            'name' => 'PlaceHolder',
            'description' => 'PlaceHolder for View, mocking events, insert block HTML ...'
        ];


        $ar['layoutedit'] = [
            'name' => 'PHTML',
            'description' => 'Edit .phtml (template) files'
        ];



        $ar['applyblocklayout'] = [
            'name' => 'Block layout',
            'description' => 'Config block layout'

        ];

        $ar['eventinserthtml'] = [
            'name' => 'Inset PHTML',
            'description' => 'Insert file.html to placeholder by trigger event'

        ];

        $ar['editslide'] = [
            'name' => 'Biên tập Slide',
            'description' => 'Biện tập slide ở trang chủ'

        ];


        $ar['pluginview'] = [
            'name' => 'Tích hợp Plugin View',
            'description' => 'Tích hợp các Plugin View vào template'

        ];

        $ar['listenerglobal'] = [
            'name' => 'Các Listener Global',
            'description' => 'Tích hợp các Plugin Listener vào template'

        ];

        $this->setListaction($ar);
    }


    function index($id)
    {
        if (!$this->ctrl->isGranted(RBAC_PERMISSION::LAYOUT_EDIT))
            return MessageBox::viewNoPermission($this->ctrl->getEvent(), Common::translate('Not permission granted'). ' : '. RBAC_PERMISSION::LAYOUT_EDIT);

        $view = $this->createView(__DIR__, __CLASS__, __FUNCTION__);

        $view->setVariables(['list' => $this->getListaction()]);
        return $view;
    }


    //HOLDER
    function placeholder($id)
    {

        if (!$this->ctrl->isGranted(RBAC_PERMISSION::LAYOUT_EDIT))
            return MessageBox::viewNoPermission($this->ctrl->getEvent(), Common::translate('Not permission granted').' : '. RBAC_PERMISSION::LAYOUT_EDIT);

        /**
         * @var $placeholderManager PlaceholderManager
         */
        $placeholderManager = $this->serviceManager->get(PlaceholderManager::class);



        $id = $this->ctrl->params()->fromRoute('id');
        $obs = $placeholderManager->allHolder();
        $item = null;

        $listevent = [];
        foreach ($obs as $ob) { $listevent[] = $ob; }

        $request = $this->ctrl->getRequest();
        $form = new FormHolder('edit');



        if ($id > 0)
        {

            $item = $placeholderManager->find($id);

            if ($item == null)
            {
                return MessageBox::htmlMessage(Common::translate('Not found').' : '.$id);
            }

            if ($request->isPost())
            {
                $post = $request->getPost();

                if ($post->action == 'Delete') {
                    return $this->ctrl->askBeforeDone(
                        'Delete keyholder: '.$item->getName(),
                        $this->ctrl->url()->fromRoute('admin', ['plugin' => 'layout', 'act' => 'holderdelete', 'id' =>$id]),
                        ['id' => $id, 'confirmdel' => 'confirmdel']

                    );
                }

                $form->addNoRecordExistsName(['field'=>'placeholder_id', 'value' => $item->getPlaceholderId()]);
                $form->setData($post);

               if ($form->isValid())
               {
                   $data = $form->getData();
                   $item->exchangeArray($data);
                   $placeholderManager->update($item->getArrayCopy(), $id);
                   return $this->ctrl->redirect()->toRoute('admin', ['plugin' => 'layout', 'act' => 'placeholder']);
               }
            }
            else
                $form->bind($item);


            $view = $this->createView(__DIR__, __CLASS__, 'holderedit');
            $view->setVariables(['item' => $item,  'form' => $form]);
            return $view;

        }

        $view = $this->createView(__DIR__, __CLASS__, __FUNCTION__);
        $view->setVariables(['listevent'=>$listevent,'item' => $item]);
        return $view;
    }

    function holderdelete($id)
    {
        if ($this->ctrl->isConfirm(['id' => $id, 'confirmdel' => 'confirmdel']))
        {
            /**
             * @var $holderManager HolderManager
             */
            $holderManager = $this->serviceManager->get(PlaceholderManager::class);
            $holderManager->delete($id);


            return MessageBox::redirectMgs('Đã xóa', 
                $this->ctrl->url()->fromRoute('admin', ['plugin' => 'layout', 'act' => 'placeholder']),
                'Thông báo',
                2

            );
        }
        return MessageBox::redirectMgs('Cancel Delete', 
            $this->ctrl->url()->fromRoute('admin', ['plugin' => 'layout', 'act' => 'placeholder', 'id' =>$id]),
            'Notification',
            2

        );
    }

    function newholder()
    {
        if (!$this->ctrl->isGranted(RBAC_PERMISSION::LAYOUT_EDIT))
            return MessageBox::viewNoPermission($this->ctrl->getEvent(), Common::translate('Not permission granted').' : '. RBAC_PERMISSION::LAYOUT_EDIT);

        if ($this->ctrl->getRequest()->isGet())
        {
            return $this->ctrl->askBeforeDone(
                'Are you sure to create new placeholder?',
                $this->url('layout', 'newholder'),
                ['suretocreatenew' => 'suretocreatenew']
            );
        }
        else {
            if ($this->ctrl->getRequest()->isPost())
                if ($this->ctrl->isConfirm(['suretocreatenew' => 'suretocreatenew']))
                {
                    /**
                     * @var $holderManager PlaceholderManager
                     */
                    $holderManager = $this->serviceManager->get(PlaceholderManager::class);

                    $id = $holderManager->createnewholder();
                    return $this->ctrl->redirect()->toRoute('admin', ['plugin' => 'layout', 'act' => 'placeholder', 'id' =>$id]);
                }
        }

        return $this->ctrl->redirect()->toRoute('admin', ['plugin' => 'layout', 'act' => 'placeholder']);

    }

    //EDIT PHTML

    function layoutedit()
    {
        if (!$this->ctrl->isGranted(RBAC_PERMISSION::LAYOUT_EDIT))
            return MessageBox::viewNoPermission($this->ctrl->getEvent(), Common::translate('Not permission granted').' : '. RBAC_PERMISSION::LAYOUT_EDIT);
        $request = $this->ctrl->getRequest();

        $csrflayoutedit = new \Zend\Form\Element\Csrf('csrflayoutedit',['csrf_options'=>['timeout'=>'1200']] );
        if ($request->isPost()) {
            //CHECK CSRF
            if (!$csrflayoutedit->getCsrfValidator()->isValid($this->ctrl->params()->fromPost('csrflayoutedit'))) {
                foreach ($csrflayoutedit->getCsrfValidator()->getMessages() as $mgs) {
                    echo $mgs;
               }
               die;
            }

            if ($this->ctrl->params()->fromPost('filecreatenew', null) != null)
            {
                $filename = $this->ctrl->params()->fromPost('filecreatenew', null);
                return $this->ctrl->askBeforeDone('Create new file:'.$filename,
                    $this->url('layout', 'addphtml'),
                    ['filecreatenew' => $filename, 'createnew' => 'createnewok']
                    );
            }



            $fileedit = '';
            $filecontent = '';
            $action  = '';

            $fileedit = $this->ctrl->params()->fromPost('file','');
            $action = $this->ctrl->params()->fromPost('action','Edit');
            $filecontent = $this->ctrl->params()->fromPost('filecontent','');
            $cdir = realpath(Common::$cf->CF->common->pathtemplatedefault);

            if ($action == 'Edit')
            {
                $filecontent = file_get_contents($cdir.'/'.$fileedit);
            }
            else if ($action == 'Save')
            {
                //chmod($cdir.'/'.$fileedit,0777);
                $filename = realpath($cdir.'/'.$fileedit);
                $byte = file_put_contents($filename, $filecontent);
                if ($byte === false)
                    throw new \Exception("Can not save: $filename");
 
            }
            else if ($action == 'Delete')
            {
                return $this->ctrl->askBeforeDone('Delete file:'.$fileedit,
                    $this->url('layout', 'deletephtml'),
                    ['filecreatenew' => $fileedit, 'delete' => 'delete']
                );



            }

            $view = $this->createView(__DIR__, __CLASS__, __FUNCTION__);
            $view->setVariables([
                'fileedit' => $fileedit,
                'filecontent' =>$filecontent
            ]);
            return $view;






            
        }
        else {
            //View List
            $cdir = realpath(Common::$cf->CF->common->pathtemplatedefault);
            $files = [];
            $folders = [];
            $getfile = function($cdir,$sub='') use(&$files,&$getfile) {
                $handle = opendir($cdir);
                if ($handle)
                {

                    while (false !== ($entry = readdir($handle)))
                    {
                        if ($entry != "." && $entry != ".." && $entry != '.htaccess')
                        {
                            if (is_dir($cdir . '/' . $entry))
                            {

                                $getfile($cdir.'/'.$entry,$sub.'/'.$entry);// $folders[] = $entry;
                            }
                            else
                                $files[] = $sub.'/'.$entry;
                        }
                    }

                    closedir($handle);

                }
            };
            $getfile($cdir);
            sort($files);

            //Group by folder
            $fs = [];
            foreach ($files as $file) {
                $f = explode('/', $file);
                $folder = $f[1];
                if (!isset($fs[$folder]))
                    $fs[$folder] = [];
                unset($f[0], $f[1]);
                $f = implode('/', $f);

                $fs[$folder][$f] = $file;
            }
            $view = $this->createView(__DIR__, __CLASS__, 'phtmlindex', 'layoutedit');
            $view->setVariables([ 'files' => $fs ]);
            return $view;
        }




    }

    function deletephtml() {
        if (!$this->ctrl->isGranted(RBAC_PERMISSION::LAYOUT_EDIT))
            return MessageBox::viewNoPermission($this->ctrl->getEvent(), Common::translate('Not permission granted').' : '. RBAC_PERMISSION::LAYOUT_EDIT);

        $request = $this->ctrl->getRequest();
        if ($request->isPost()) {
            $filename = $this->ctrl->params()->fromPost('filecreatenew', null);
            if ($this->ctrl->isConfirm(['filecreatenew' => $filename, 'delete' => 'delete'])) {

                $filedelete = $filename;

                throw new \Exception('(TODO)Check not use template before delete file:'.$filedelete);

//                $rows = $this->ctrl->dbAdapter->get_rows_select($this->ctrl->dbAdapter->sql->select('blockhtmlevent')->columns(['Block']));
//                foreach ($rows as $ctrl)
//                    if ($cfile == $ctrl['Block'])
//                    {
//                        $candel = false;
//                    }
//                if ($candel) @unlink($cdir.'/'.$fileedit);
//                $fileedit = '';



                $cdir = realpath(Common::$cf->CF->common->pathtemplatedefault);



            }
        }

        return $this->ctrl->redirect()->toRoute('admin', ['plugin' => 'layout', 'act' => 'layoutedit']);
    }

    function addphtml() {
        if (!$this->ctrl->isGranted(RBAC_PERMISSION::LAYOUT_EDIT))
            return MessageBox::viewNoPermission($this->ctrl->getEvent(), Common::translate('Not permission granted').' : '. RBAC_PERMISSION::LAYOUT_EDIT);

        $request = $this->ctrl->getRequest();
        if ($request->isPost()) {
            $filename = $this->ctrl->params()->fromPost('filecreatenew', null);
            if ($this->ctrl->isConfirm(['filecreatenew' => $filename, 'createnew' => 'createnewok'])) {


                $filterFilename = new FilterChain(
                    ['filters' => [
                        ['name' => PregReplace::class,   'options' => ['pattern'=>'/\.\./','replacement'=>'']],
                        ['name' => StringToLower::class, 'options' => [ 'encoding' => 'UTF-8' ]],
                        ['name' => StringTrim::class],
                        ['name' => StringToLower::class]
                    ]
                    ]
                );

                $newfile = $filterFilename->filter($filename);

                $cdir = realpath(Common::$cf->CF->common->pathtemplatedefault);
                $newfile = $cdir.'/'.$newfile;

                $validatorChain = new ValidatorChain();
                $validatorChain->attachByName(NotExists::class, []);
                $validatorChain->attachByName(ExtensionAllow::class, ['extension' => 'phtml']);

                if (!$validatorChain->isValid($newfile)) {
                    foreach ($validatorChain->getMessages() as $mgs) {
                        echo $mgs;
                    }
                    die;
                }

                $dircreate = pathinfo($newfile,PATHINFO_DIRNAME);
                if (!file_exists($dircreate))
                    @mkdir($dircreate);
                $byte = file_put_contents($newfile, 'NEW FILE BLOCK');
                if ($byte === false)
                    throw new \Exception("Can not save: $newfile");



            }
        }

        return $this->ctrl->redirect()->toRoute('admin', ['plugin' => 'layout', 'act' => 'layoutedit']);
    }


    //EVENT INSERT HTML
    function eventinserthtml($id) {
        if (!$this->ctrl->isGranted(RBAC_PERMISSION::LAYOUT_EDIT))
            return MessageBox::viewNoPermission($this->ctrl->getEvent(), Common::translate('Not permission granted').' : '. RBAC_PERMISSION::LAYOUT_EDIT);


        /**
         * @var $inserthtmlmanager InsertHtmlManager
         */
        $inserthtmlmanager = $this->serviceManager->get(InsertHtmlManager::class);

        $obs = $inserthtmlmanager->allEventHTMLInsert();
        $listeventblock = new \ArrayObject();
        foreach ($obs as $ob)
        {
            $listeventblock[] = $ob;
        }

        if ($this->ctrl->getRequest()->isPost())
        {
            $inserthtmlmanager->exportconfig();
        }


        $view = $this->createView(__DIR__, __CLASS__, __FUNCTION__);
        $view->setVariables(['listeventblock' => $listeventblock]);
        return $view;
    }

    function eventinserthtmledit($id)
    {
        if (!$this->ctrl->isGranted(RBAC_PERMISSION::LAYOUT_EDIT))
            return MessageBox::viewNoPermission($this->ctrl->getEvent(), Common::translate('Not permission granted').' : '. RBAC_PERMISSION::LAYOUT_EDIT);

        /**
         * @var $inserthtmlmanager InsertHtmlManager
         * @var $ControllerElement Select
         * @var $EventElement Select
         * @var $BlockElement Select
         * @var $ActiveElement Checkbox
         * @var $ev InsertHTML
         */
        $inserthtmlmanager = $this->serviceManager->get(InsertHtmlManager::class);

        $form = new FromInsertHtml('edit');

        $ev = $inserthtmlmanager->find($id);
        if ($ev == null)
        return MessageBox::redirectMgs('Not found',
            $this->ctrl->url()->fromRoute('admin', ['plugin' => 'layout', 'act' => 'eventinserthtml'])

        );
        $request = $this->ctrl->getRequest();
        if ($request->isPost()) {
            $params = $this->ctrl->params();

            $action = $params->fromPost('action','Save');
            if ($action == 'Delete')
            {
                return $this->ctrl->askBeforeDone(
                    'Delete Event?',
                    $this->ctrl->url()->fromRoute('admin', ['plugin' => 'layout', 'act' => 'eventinserthtmldelete', 'id' => $id]),
                    ['id' => $id, 'deleteinsertevent' => $id]
                );
            }



            $form->setData($request->getPost());
            if ($form->isValid()) {
                $data = $form->getData();
                $ev->exchangeArray($data);
                $data = $ev->getArrayCopy();
                unset($data['ControllerAction']);

                $inserthtmlmanager->update($data, $id);
                return MessageBox::redirectMgs(
                    'Save success',
                    $this->ctrl->url()->fromRoute('admin', ['plugin'=>'layout', 'act' => 'eventinserthtml']),
                    'Save',
                    2
                );

            }
        }
        else
        $form->setData($ev->getArrayCopy());


        $valrt =  [
            'form' => $form,
            'InsertHTML' => $ev
        ];

        $view = $this->createView(__DIR__, __CLASS__, __FUNCTION__);
        $view->setVariables($valrt);
        return $view;

    }

    function eventinserthtmldelete($id)
    {
        if (!$this->ctrl->isGranted(RBAC_PERMISSION::LAYOUT_EDIT))
            return MessageBox::viewNoPermission($this->ctrl->getEvent(), Common::translate('Not permission granted').' : '. RBAC_PERMISSION::LAYOUT_EDIT);

        /**
         * @var $inserthtmlmanager InsertHtmlManager
         */
        $inserthtmlmanager = $this->serviceManager->get(InsertHtmlManager::class);


        if ($this->ctrl->isConfirm( ['id' => $id, 'deleteinsertevent' => $id]))
        {
            $inserthtmlmanager->delete($id);
            return MessageBox::redirectMgs(
                'Deleted',
                $this->ctrl->url()->fromRoute('admin', ['plugin'=>'layout', 'act' => 'eventinserthtml']),
                'Save',
                2
            );

        }
        return $this->ctrl->redirect()->toRoute('admin', ['plugin'=>'layout', 'act' => 'eventinserthtml']);


    }

    function eventinserthtmladd($id)
    {
        if (!$this->ctrl->isGranted(RBAC_PERMISSION::LAYOUT_EDIT))
            return MessageBox::viewNoPermission($this->ctrl->getEvent(), Common::translate('Not permission granted').' : '. RBAC_PERMISSION::LAYOUT_EDIT);

        /**
         * @var $inserthtmlmanager InsertHtmlManager
         * @var $ControllerElement Select
         * @var $EventElement Select
         * @var $BlockElement Select
         * @var $ActiveElement Checkbox
         * @var $ev InsertHTML
         */
        $inserthtmlmanager = $this->serviceManager->get(InsertHtmlManager::class);

        $form = new FromInsertHtml('edit');


        $request = $this->ctrl->getRequest();
        if ($request->isPost()) {
            $params = $this->ctrl->params();


            $form->setData($request->getPost());
            if ($form->isValid()) {
                $data = $form->getData();
                $ev = new InsertHTML();
                $ev->exchangeArray($data);
                $data = $ev->getArrayCopy();
                unset($data['ControllerAction']);

                $inserthtmlmanager->insertnew($data);
                return MessageBox::redirectMgs(
                    'Create success',
                    $this->ctrl->url()->fromRoute('admin', ['plugin'=>'layout', 'act' => 'eventinserthtml']),
                    'Save',
                    2
                );

            }
        }



        $valrt =  [
            'form' => $form
        ];


        $view = $this->createView(__DIR__, __CLASS__, 'eventinserthtmledit');
        $view->setVariables($valrt);

        return $view;

    }

    //BLOCK EVENT
    function applyblocklayout()
    {


        if (!$this->ctrl->isGranted(RBAC_PERMISSION::LAYOUT_EDIT))
            return MessageBox::viewNoPermission($this->ctrl->getEvent(), Common::translate('Not permission granted').' : '. RBAC_PERMISSION::LAYOUT_EDIT);

        /**
         * @var $blcoklayoutManager BlockLayoutManager
         */
        $blcoklayoutManager = $this->serviceManager->get(BlockLayoutManager::class);
        $listeventblock = $blcoklayoutManager->allBlocks();


        if ($this->ctrl->getRequest()->isPost()) {

            $blcoklayoutManager->exportconfig();

            return $this->ctrl->returnRedirect(
                'Đã lưu dữ liệu ra file block-layout-config.php',
                'Thông báo',
                $this->ctrl->url()->fromRoute('admin', ['plugin' => 'layout', 'act' => 'applyblocklayout']),
                2
            );

        }
        $view = $this->createView(__DIR__, __CLASS__, __FUNCTION__);
        $view->setVariables(['listeventblock'=>$listeventblock]);
        return $view;
    }





}